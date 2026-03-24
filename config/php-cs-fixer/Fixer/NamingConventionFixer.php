<?php

declare(strict_types=1);

namespace Niklan\PhpCsFixer\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Enforces naming conventions:
 * - Class properties (including promoted) → camelCase
 * - Function parameters (non-promoted) → snake_case
 * - Local variables → snake_case
 */
final class NamingConventionFixer extends AbstractFixer {

  private const SUPERGLOBALS = [
    '$GLOBALS', '$_SERVER', '$_GET', '$_POST', '$_FILES',
    '$_COOKIE', '$_SESSION', '$_REQUEST', '$_ENV',
  ];

  private const PROPERTY_MODIFIER_TOKENS = [
    \T_PUBLIC,
    \T_PROTECTED,
    \T_PRIVATE,
    \T_STATIC,
    \T_READONLY,
    \T_VAR,
    CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
    CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
    CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
  ];

  private const VISIBILITY_TOKENS = [
    \T_PUBLIC,
    \T_PROTECTED,
    \T_PRIVATE,
    CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
    CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
    CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
  ];

  private const TYPE_TOKENS = [
    \T_STRING,
    \T_NS_SEPARATOR,
    \T_NAME_QUALIFIED,
    \T_NAME_FULLY_QUALIFIED,
    \T_ARRAY,
    \T_CALLABLE,
    CT::T_ARRAY_TYPEHINT,
    CT::T_NULLABLE_TYPE,
    CT::T_TYPE_ALTERNATION,
    CT::T_TYPE_INTERSECTION,
    CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
    CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
  ];

  public function getName(): string {
    return 'Niklan/naming_convention';
  }

  public function getDefinition(): FixerDefinitionInterface {
    return new FixerDefinition(
      'Properties → camelCase, parameters and local variables → snake_case.',
      [
        new CodeSample(
          <<<'PHP'
            <?php

            class Foo {

              private string $my_property;

              public function __construct(
                private string $my_promoted,
              ) {}

              public function bar(string $myParam): void {
                $myVar = $this->myProperty;
                echo $myParam . $myVar;
              }

            }

            PHP,
        ),
      ],
      null,
      'Renames properties, parameters and variables, which may break external references.',
    );
  }

  public function isCandidate(Tokens $tokens): bool {
    return $tokens->isTokenKindFound(\T_VARIABLE);
  }

  #[\Override]
  public function isRisky(): bool {
    return true;
  }

  protected function applyFix(SplFileInfo $file, Tokens $tokens): void {
    $this->fixProperties($tokens);
    $this->fixFunctions($tokens);
  }

  private function fixProperties(Tokens $tokens): void {
    for ($index = 0, $count = $tokens->count(); $index < $count; $index++) {
      if (!$tokens[$index]->isGivenKind(\T_VARIABLE)) {
        continue;
      }

      if (!$this->isPropertyDeclaration($tokens, $index)) {
        continue;
      }

      $old_name = \substr($tokens[$index]->getContent(), 1);
      $new_name = self::toCamelCase($old_name);

      if ($old_name === $new_name) {
        continue;
      }

      $tokens->offsetSet($index, new Token([\T_VARIABLE, '$' . $new_name]));

      $class_bounds = $this->findEnclosingClassBounds($tokens, $index);

      if ($class_bounds !== null) {
        $this->renamePropertyUsages($tokens, $old_name, $new_name, $class_bounds[0], $class_bounds[1]);
      }

      if ($this->isInsideFunctionParameters($tokens, $index)) {
        $body_range = $this->findFunctionBodyFromParam($tokens, $index);

        if ($body_range !== null) {
          $this->renameVariableInRange($tokens, $old_name, $new_name, $body_range[0], $body_range[1]);
        }
      }
    }
  }

  private function fixFunctions(Tokens $tokens): void {
    for ($index = 0, $count = $tokens->count(); $index < $count; $index++) {
      if (!$tokens[$index]->isGivenKind([\T_FUNCTION, \T_FN])) {
        continue;
      }

      $open_paren = $tokens->getNextTokenOfKind($index, ['(']);

      if ($open_paren === null) {
        continue;
      }

      $close_paren = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $open_paren);
      $is_arrow = $tokens[$index]->isGivenKind(\T_FN);

      $params_to_rename = $this->fixParameters($tokens, $open_paren, $close_paren);

      if ($is_arrow) {
        $arrow = $tokens->getNextTokenOfKind($close_paren, [[\T_DOUBLE_ARROW]]);

        if ($arrow === null) {
          continue;
        }

        $body_start = $arrow;
        $body_end = $this->findArrowFunctionBodyEnd($tokens, $arrow);
      }
      else {
        $body_start = $tokens->getNextTokenOfKind($close_paren, ['{', ';']);

        if ($body_start === null || $tokens[$body_start]->equals(';')) {
          continue;
        }

        $body_end = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $body_start);
      }

      foreach ($params_to_rename as $old_name => $new_name) {
        $this->renameVariableInRange($tokens, $old_name, $new_name, $body_start, $body_end);
      }

      if (!$is_arrow) {
        $this->fixLocalVariables($tokens, $open_paren, $close_paren, $body_start, $body_end);
      }
    }
  }

  private function fixParameters(Tokens $tokens, int $open_paren, int $close_paren): array {
    $params_to_rename = [];

    for ($i = $open_paren + 1; $i < $close_paren; $i++) {
      if (!$tokens[$i]->isGivenKind(\T_VARIABLE)) {
        continue;
      }

      if ($this->hasVisibilityModifier($tokens, $i)) {
        continue;
      }

      $old_name = \substr($tokens[$i]->getContent(), 1);
      $new_name = self::toSnakeCase($old_name);

      if ($old_name === $new_name) {
        continue;
      }

      $tokens->offsetSet($i, new Token([\T_VARIABLE, '$' . $new_name]));
      $params_to_rename[$old_name] = $new_name;
    }

    return $params_to_rename;
  }

  private function fixLocalVariables(Tokens $tokens, int $open_paren, int $close_paren, int $body_start, int $body_end): void {
    $param_names = [];

    for ($i = $open_paren + 1; $i < $close_paren; $i++) {
      if ($tokens[$i]->isGivenKind(\T_VARIABLE)) {
        $param_names[] = $tokens[$i]->getContent();
      }
    }

    $renamed = [];

    for ($i = $body_start + 1; $i < $body_end; $i++) {
      if (!$tokens[$i]->isGivenKind(\T_VARIABLE)) {
        continue;
      }

      $var_content = $tokens[$i]->getContent();

      if ($var_content === '$this' || \in_array($var_content, self::SUPERGLOBALS, true) || \in_array($var_content, $param_names, true)) {
        continue;
      }

      $prev = $tokens->getPrevMeaningfulToken($i);

      if ($prev !== null && $tokens[$prev]->isGivenKind(\T_OBJECT_OPERATOR)) {
        continue;
      }

      $old_name = \substr($var_content, 1);

      if (isset($renamed[$old_name])) {
        $tokens->offsetSet($i, new Token([\T_VARIABLE, '$' . $renamed[$old_name]]));

        continue;
      }

      $new_name = self::toSnakeCase($old_name);

      if ($old_name === $new_name) {
        continue;
      }

      $renamed[$old_name] = $new_name;
      $tokens->offsetSet($i, new Token([\T_VARIABLE, '$' . $new_name]));
    }
  }

  private function isPropertyDeclaration(Tokens $tokens, int $index): bool {
    $check = $tokens->getPrevMeaningfulToken($index);

    while ($check !== null) {
      $token = $tokens[$check];

      if ($token->isGivenKind(self::PROPERTY_MODIFIER_TOKENS)) {
        return true;
      }

      if ($token->isGivenKind(self::TYPE_TOKENS)) {
        $check = $tokens->getPrevMeaningfulToken($check);

        continue;
      }

      break;
    }

    return false;
  }

  private function hasVisibilityModifier(Tokens $tokens, int $index): bool {
    $check = $tokens->getPrevMeaningfulToken($index);

    while ($check !== null) {
      $token = $tokens[$check];

      if ($token->isGivenKind(self::VISIBILITY_TOKENS)) {
        return true;
      }

      if (
        $token->isGivenKind(self::TYPE_TOKENS)
        || $token->isGivenKind(\T_READONLY)
        || $token->isGivenKind(\T_STATIC)
      ) {
        $check = $tokens->getPrevMeaningfulToken($check);

        continue;
      }

      break;
    }

    return false;
  }

  private function isInsideFunctionParameters(Tokens $tokens, int $index): bool {
    $depth = 0;

    for ($i = $index - 1; $i >= 0; $i--) {
      if ($tokens[$i]->equals(')')) {
        $depth++;
      }
      elseif ($tokens[$i]->equals('(')) {
        if ($depth === 0) {
          $prev = $tokens->getPrevMeaningfulToken($i);

          return $prev !== null && $tokens[$prev]->isGivenKind([\T_FUNCTION, \T_FN]);
        }
        $depth--;
      }
    }

    return false;
  }

  private function findFunctionBodyFromParam(Tokens $tokens, int $param_index): ?array {
    $depth = 0;

    for ($i = $param_index + 1, $count = $tokens->count(); $i < $count; $i++) {
      if ($tokens[$i]->equals('(')) {
        $depth++;
      }
      elseif ($tokens[$i]->equals(')')) {
        if ($depth === 0) {
          $body_start = $tokens->getNextTokenOfKind($i, ['{', ';']);

          if ($body_start === null || $tokens[$body_start]->equals(';')) {
            return null;
          }

          $body_end = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $body_start);

          return [$body_start, $body_end];
        }
        $depth--;
      }
    }

    return null;
  }

  private function findEnclosingClassBounds(Tokens $tokens, int $index): ?array {
    $depth = 0;

    for ($i = $index - 1; $i >= 0; $i--) {
      if ($tokens[$i]->equals('}')) {
        $depth++;
      }
      elseif ($tokens[$i]->equals('{')) {
        if ($depth === 0) {
          $end = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $i);

          return [$i, $end];
        }
        $depth--;
      }
    }

    return null;
  }

  private function findArrowFunctionBodyEnd(Tokens $tokens, int $arrow_index): int {
    $depth_paren = 0;
    $depth_bracket = 0;
    $count = $tokens->count();

    for ($i = $arrow_index + 1; $i < $count; $i++) {
      $token = $tokens[$i];

      if ($token->equals('(')) {
        $depth_paren++;
      }
      elseif ($token->equals(')')) {
        if ($depth_paren === 0) {
          return $i - 1;
        }
        $depth_paren--;
      }
      elseif ($token->equals('[')) {
        $depth_bracket++;
      }
      elseif ($token->equals(']')) {
        if ($depth_bracket === 0) {
          return $i - 1;
        }
        $depth_bracket--;
      }
      elseif ($token->equals(';') || ($token->equals(',') && $depth_paren === 0 && $depth_bracket === 0)) {
        return $i - 1;
      }
    }

    return $count - 1;
  }

  private function renamePropertyUsages(Tokens $tokens, string $old_name, string $new_name, int $start, int $end): void {
    for ($i = $start; $i <= $end; $i++) {
      if (!$tokens[$i]->isGivenKind(\T_STRING) || $tokens[$i]->getContent() !== $old_name) {
        continue;
      }

      $prev = $tokens->getPrevMeaningfulToken($i);

      if ($prev === null || !$tokens[$prev]->isGivenKind(\T_OBJECT_OPERATOR)) {
        continue;
      }

      $prev_prev = $tokens->getPrevMeaningfulToken($prev);

      if ($prev_prev === null || $tokens[$prev_prev]->getContent() !== '$this') {
        continue;
      }

      $tokens->offsetSet($i, new Token([\T_STRING, $new_name]));
    }
  }

  private function renameVariableInRange(Tokens $tokens, string $old_name, string $new_name, int $start, int $end): void {
    $old_var = '$' . $old_name;

    for ($i = $start; $i <= $end; $i++) {
      if (!$tokens[$i]->isGivenKind(\T_VARIABLE) || $tokens[$i]->getContent() !== $old_var) {
        continue;
      }

      $prev = $tokens->getPrevMeaningfulToken($i);

      if ($prev !== null && $tokens[$prev]->isGivenKind(\T_OBJECT_OPERATOR)) {
        continue;
      }

      $tokens->offsetSet($i, new Token([\T_VARIABLE, '$' . $new_name]));
    }
  }

  private static function toCamelCase(string $name): string {
    if (!\str_contains($name, '_')) {
      return $name;
    }

    $prefix = '';
    $stripped = \ltrim($name, '_');

    if ($stripped !== $name) {
      $prefix = \substr($name, 0, \strlen($name) - \strlen($stripped));
    }

    $parts = \explode('_', $stripped);
    $result = \strtolower(\array_shift($parts));

    foreach ($parts as $part) {
      if ($part !== '') {
        $result .= \ucfirst(\strtolower($part));
      }
    }

    return $prefix . $result;
  }

  private static function toSnakeCase(string $name): string {
    if (\strtolower($name) === $name) {
      return $name;
    }

    $prefix = '';
    $stripped = \ltrim($name, '_');

    if ($stripped !== $name) {
      $prefix = \substr($name, 0, \strlen($name) - \strlen($stripped));
    }

    $result = \preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $stripped);
    $result = \preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $result);
    $result = \strtolower($result);

    return $prefix . $result;
  }

}
