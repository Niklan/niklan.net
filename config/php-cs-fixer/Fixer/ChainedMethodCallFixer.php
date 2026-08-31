<?php

declare(strict_types=1);

namespace App\PhpCsFixer\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;

/**
 * Enforces the project's "sufficient context" rule for fluent chains:
 * - A named variable is already sufficient context; bare `$this` is not and
 *   is extended by exactly one property/method access before anything else
 *   may break.
 * - Below the configured line length the whole chain collapses onto one
 *   line; at or above it, every step after the head breaks onto its own
 *   line.
 * - Chains containing a foreign newline (e.g. a nested chain passed as an
 *   argument) are left untouched — their length can't be judged reliably.
 * - Chains embedded in a control structure's condition (`if`, `foreach`,
 *   `while`, `for`, `switch`, `match`, `catch`) or in a larger boolean/
 *   comparison expression are left untouched entirely — extracting a
 *   well-named variable there is the developer's call, not this fixer's.
 */
final class ChainedMethodCallFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface {

  private const array OBJECT_OPERATOR_KINDS = [\T_OBJECT_OPERATOR, \T_NULLSAFE_OBJECT_OPERATOR];
  private const array HEAD_BLOCKING_KINDS = [\T_OBJECT_OPERATOR, \T_NULLSAFE_OBJECT_OPERATOR, \T_DOUBLE_COLON];
  private const array CONTROL_STRUCTURE_KINDS = [\T_IF, \T_ELSEIF, \T_WHILE, \T_FOR, \T_FOREACH, \T_SWITCH, \T_MATCH, \T_CATCH];
  private const array BOOLEAN_EXPRESSION_KINDS = [
    \T_BOOLEAN_AND, \T_BOOLEAN_OR, \T_LOGICAL_AND, \T_LOGICAL_OR, \T_LOGICAL_XOR,
    \T_IS_EQUAL, \T_IS_NOT_EQUAL, \T_IS_IDENTICAL, \T_IS_NOT_IDENTICAL,
    \T_IS_SMALLER_OR_EQUAL, \T_IS_GREATER_OR_EQUAL, \T_SPACESHIP, \T_COALESCE,
  ];
  private const array BOOLEAN_EXPRESSION_SYMBOLS = ['!', '?', ':', '<', '>'];
  private const int DEFAULT_LINE_LENGTH = 120;

  private WhitespacesFixerConfig $whitespacesConfig;

  /**
   * @var array{line_length: int}
   */
  private array $configuration = ['line_length' => self::DEFAULT_LINE_LENGTH];

  public function getName(): string {
    return 'App/chained_method_call_head';
  }

  public function getDefinition(): FixerDefinitionInterface {
    return new FixerDefinition(
      summary: 'Below the configured line length a fluent chain collapses onto one line; at or above it, the chain keeps just enough context on its head (a named variable, or $this extended by exactly one property/method access), then breaks before every call after that.',
      codeSamples: [
        new CodeSample(
          code: <<<'PHP'
            <?php

            class Foo {

              public function bar() {
                $this
                  ->database
                  ->select('table', 'alias')
                  ->execute();

                $this->database->select('table', 'alias')->condition('field', $value)->execute();
              }

            }

            PHP,
        ),
      ],
    );
  }

  public function configure(array $configuration): void {
    $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);
  }

  public function getConfigurationDefinition(): FixerConfigurationResolverInterface {
    return new FixerConfigurationResolver([
      (new FixerOptionBuilder('line_length', 'Chains at or above this length break one call per line; shorter ones collapse onto a single line.'))
        ->setAllowedTypes(['int'])
        ->setDefault(self::DEFAULT_LINE_LENGTH)
        ->getOption(),
    ]);
  }

  public function setWhitespacesConfig(WhitespacesFixerConfig $config): void {
    $this->whitespacesConfig = $config;
  }

  public function isCandidate(Tokens $tokens): bool {
    return $tokens->isAnyTokenKindsFound(self::OBJECT_OPERATOR_KINDS);
  }

  protected function applyFix(SplFileInfo $file, Tokens $tokens): void {
    for ($index = $tokens->count() - 1; $index > 0; --$index) {
      if (!$tokens[$index]->isGivenKind(\T_VARIABLE) || !$this->isChainBase($tokens, $index)) {
        continue;
      }

      $this->fixChain($tokens, $index);
    }
  }

  private function isChainBase(Tokens $tokens, int $index): bool {
    $prev = $tokens->getPrevMeaningfulToken($index);

    if ($prev !== null && $tokens[$prev]->isGivenKind(self::HEAD_BLOCKING_KINDS)) {
      return FALSE;
    }

    $next = $tokens->getNextMeaningfulToken($index);

    return $next !== null && $tokens[$next]->isGivenKind(self::OBJECT_OPERATOR_KINDS);
  }

  private function fixChain(Tokens $tokens, int $index): void {
    if ($this->isInsideControlStructureCondition($tokens, $index)) {
      return;
    }

    $is_this = $tokens[$index]->getContent() === '$this';
    $this_op = $is_this ? $tokens->getNextMeaningfulToken($index) : NULL;
    $head_end = $is_this ? $this->resolveThisHeadEnd($tokens, $index) : $index;

    $next_op = $tokens->getNextMeaningfulToken($head_end);
    $has_further_chain = $next_op !== null && $tokens[$next_op]->isGivenKind(self::OBJECT_OPERATOR_KINDS);

    $chain_end = $head_end;
    $boundaries = [];

    if ($has_further_chain) {
      [$chain_end, $boundaries] = $this->walkChain($tokens, $next_op);
    }

    $recognized = $this_op !== null ? [$this_op, ...$boundaries] : $boundaries;

    if ($this->hasForeignNewline($tokens, $index, $chain_end, $recognized)
      || $this->isEmbeddedInBooleanExpression($tokens, $index, $chain_end)
    ) {
      return;
    }

    if ($this_op !== null) {
      $this->collapseNewlineBetween($tokens, $index, $this_op);
    }

    if ($boundaries === []) {
      return;
    }

    $length = $this->getColumnOffset($tokens, $index)
      + $this->collapsedLength($tokens, $index, $chain_end, $boundaries)
      + $this->getTrailingLength($tokens, $chain_end);
    $want_newline = $length > $this->configuration['line_length'];

    // A single trailing step (e.g. `$a->id`, `$this->x->y()`) is too trivial
    // to justify forcing a *new* break just because the surrounding
    // expression is long for unrelated reasons — but simplifying it back to
    // one line, when it already fits, is always safe.
    if (\count($boundaries) < 2 && $want_newline) {
      return;
    }

    // A chain used as an inline argument to another call (e.g.
    // `foo($chain->a()->b())`) can't be reliably re-indented from a single
    // line's indentation alone — extracting a variable there is the
    // developer's call, not this fixer's.
    if ($want_newline && !$this->isSafeToIndent($tokens, $index)) {
      return;
    }

    $indent = $this->getLineIndent($tokens, $index) . $this->whitespacesConfig->getIndent();

    foreach (\array_reverse($boundaries) as $op_index) {
      $this->setBoundary($tokens, $op_index, $want_newline, $indent);
    }
  }

  /**
   * True if $index sits inside the parenthesized condition of a control
   * structure (`if`, `foreach`, `while`, `for`, `switch`, `match`, `catch`),
   * at any nesting depth. Reformatting there is the developer's call — the
   * usual fix is extracting a well-named variable, not reflowing the chain.
   */
  private function isInsideControlStructureCondition(Tokens $tokens, int $index): bool {
    $search_from = $index;

    while (TRUE) {
      $open_paren = $this->findEnclosingParen($tokens, $search_from);

      if ($open_paren === null) {
        return FALSE;
      }

      $prev = $tokens->getPrevMeaningfulToken($open_paren);

      if ($prev !== null && $tokens[$prev]->isGivenKind(self::CONTROL_STRUCTURE_KINDS)) {
        return TRUE;
      }

      $search_from = $open_paren;
    }
  }

  private function findEnclosingParen(Tokens $tokens, int $index): ?int {
    $depth = 0;

    for ($i = $index - 1; $i >= 0; --$i) {
      if ($tokens[$i]->equals(')')) {
        $depth++;
        continue;
      }

      if (!$tokens[$i]->equals('(')) {
        continue;
      }

      if ($depth > 0) {
        $depth--;
        continue;
      }

      return $i;
    }

    return NULL;
  }

  /**
   * True if the chain is used as an operand of a boolean/comparison/ternary
   * expression (e.g. `!$a->b()->c()`, `$a->b()->c() === $x`) rather than
   * being the statement's own value — the length of the surrounding
   * expression isn't the chain's fault, so it's left alone.
   */
  private function isEmbeddedInBooleanExpression(Tokens $tokens, int $start, int $end): bool {
    $prev = $tokens->getPrevMeaningfulToken($start);

    if ($prev !== null && $this->isBooleanExpressionToken($tokens, $prev)) {
      return TRUE;
    }

    $next = $tokens->getNextMeaningfulToken($end);

    return $next !== null && $this->isBooleanExpressionToken($tokens, $next);
  }

  private function isBooleanExpressionToken(Tokens $tokens, int $index): bool {
    if ($tokens[$index]->isGivenKind(self::BOOLEAN_EXPRESSION_KINDS)) {
      return TRUE;
    }

    foreach (self::BOOLEAN_EXPRESSION_SYMBOLS as $symbol) {
      if ($tokens[$index]->equals($symbol)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Resolves where a bare `$this` head ends once extended by exactly one
   * property/method access — read-only, does not mutate $tokens. Bare
   * `$this` never carries enough context, regardless of line length.
   */
  private function resolveThisHeadEnd(Tokens $tokens, int $index): int {
    $op = $tokens->getNextMeaningfulToken($index);
    $name = $tokens->getNextMeaningfulToken($op);

    if ($name === null || !$tokens[$name]->isGivenKind(\T_STRING)) {
      return $index;
    }

    $after_name = $tokens->getNextMeaningfulToken($name);

    if ($after_name !== null && $tokens[$after_name]->equals('(')) {
      return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS, $after_name);
    }

    return $name;
  }

  /**
   * Walks a `->a->b()->c` chain starting at its first operator.
   *
   * @return array{0: int, 1: list<int>}
   *   The index of the chain's final token, and the operator index of each
   *   step, in order.
   */
  private function walkChain(Tokens $tokens, int $op_index): array {
    $current_op = $op_index;
    $boundaries = [];

    while (TRUE) {
      $name = $tokens->getNextMeaningfulToken($current_op);

      if ($name === null || !$tokens[$name]->isGivenKind([\T_STRING, \T_VARIABLE])) {
        return [$current_op, $boundaries];
      }

      $boundaries[] = $current_op;

      $after_name = $tokens->getNextMeaningfulToken($name);
      $end = $after_name !== null && $tokens[$after_name]->equals('(')
        ? $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS, $after_name)
        : $name;

      $next = $tokens->getNextMeaningfulToken($end);

      if ($next === null || !$tokens[$next]->isGivenKind(self::OBJECT_OPERATOR_KINDS)) {
        return [$end, $boundaries];
      }

      $current_op = $next;
    }
  }

  /**
   * @param list<int> $boundaries
   *
   * True if [$start, $end] contains a newline that isn't one of this
   * chain's own step boundaries (e.g. a nested chain passed as an
   * argument) — its length can't be judged reliably, so the chain is left
   * untouched.
   */
  private function hasForeignNewline(Tokens $tokens, int $start, int $end, array $boundaries): bool {
    $boundary_set = \array_flip($boundaries);

    for ($i = $start + 1; $i < $end; $i++) {
      if (!$tokens[$i]->isWhitespace() || !\str_contains($tokens[$i]->getContent(), "\n")) {
        continue;
      }

      if (!isset($boundary_set[$i + 1])) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * @param list<int> $boundaries
   */
  private function collapsedLength(Tokens $tokens, int $start, int $end, array $boundaries): int {
    $boundary_whitespace = [];

    foreach ($boundaries as $op) {
      if ($tokens[$op - 1]->isWhitespace()) {
        $boundary_whitespace[$op - 1] = TRUE;
      }
    }

    $length = 0;

    for ($i = $start; $i <= $end; $i++) {
      if (isset($boundary_whitespace[$i])) {
        continue;
      }

      $length += \strlen($tokens[$i]->getContent());
    }

    return $length;
  }

  /**
   * True unless $index sits inside an opening `(` or `[` that starts on the
   * same line (i.e. the chain is itself a parameter/argument to a call, an
   * array key, or a grouping) — a plain `=`, `return`, `,` etc. earlier on
   * the line is fine, but an unclosed bracket means indentation can't be
   * judged reliably from this line alone.
   */
  private function isSafeToIndent(Tokens $tokens, int $index): bool {
    $depth = 0;

    for ($i = $index - 1; $i >= 0; --$i) {
      $token = $tokens[$i];

      if ($token->isWhitespace() && \str_contains($token->getContent(), "\n")) {
        return TRUE;
      }

      if ($token->equals(')') || $token->equals(']')) {
        $depth++;

        continue;
      }

      if ($token->equals('(') || $token->equals('[')) {
        if ($depth === 0) {
          return FALSE;
        }

        $depth--;
      }
    }

    return TRUE;
  }

  /**
   * The number of characters from the start of the line up to (but not
   * including) $index.
   */
  private function getColumnOffset(Tokens $tokens, int $index): int {
    $offset = 0;

    for ($i = $index - 1; $i >= 0; --$i) {
      $content = $tokens[$i]->getContent();
      $newline_position = \strrpos($content, "\n");

      if ($newline_position !== FALSE) {
        return $offset + \strlen($content) - $newline_position - 1;
      }

      $offset += \strlen($content);
    }

    return $offset;
  }

  /**
   * The number of characters remaining on the line after $index (e.g. a
   * trailing `;`, `,`, `)`, or comment).
   */
  private function getTrailingLength(Tokens $tokens, int $index): int {
    $length = 0;

    for ($i = $index + 1, $count = $tokens->count(); $i < $count; ++$i) {
      $content = $tokens[$i]->getContent();
      $newline_position = \strpos($content, "\n");

      if ($newline_position !== FALSE) {
        return $length + $newline_position;
      }

      $length += \strlen($content);
    }

    return $length;
  }

  private function collapseNewlineBetween(Tokens $tokens, int $start, int $end): void {
    for ($i = $start + 1; $i < $end; $i++) {
      if ($tokens[$i]->isWhitespace() && \str_contains($tokens[$i]->getContent(), "\n")) {
        $tokens->clearAt($i);
      }
    }
  }

  private function getLineIndent(Tokens $tokens, int $index): string {
    for ($i = $index; $i >= 0; --$i) {
      if (!$tokens[$i]->isWhitespace()) {
        continue;
      }

      if (\preg_match('/\R([ \t]*)$/', $tokens[$i]->getContent(), $matches) === 1) {
        return $matches[1];
      }
    }

    return '';
  }

  private function setBoundary(Tokens $tokens, int $op_index, bool $want_newline, string $indent): void {
    $has_whitespace = $tokens[$op_index - 1]->isWhitespace();

    if (!$want_newline) {
      if ($has_whitespace) {
        $tokens->clearAt($op_index - 1);
      }

      return;
    }

    // Already on its own line: its indentation may reflect nesting (e.g.
    // being a call argument) that a line-based recompute can't reliably
    // reproduce, so it's left exactly as-is rather than risk getting it
    // wrong.
    if ($has_whitespace && \str_contains($tokens[$op_index - 1]->getContent(), "\n")) {
      return;
    }

    $newline = $this->whitespacesConfig->getLineEnding() . $indent;

    if ($has_whitespace) {
      $tokens[$op_index - 1] = new Token([\T_WHITESPACE, $newline]);

      return;
    }

    $tokens->insertAt($op_index, new Token([\T_WHITESPACE, $newline]));
  }

}
