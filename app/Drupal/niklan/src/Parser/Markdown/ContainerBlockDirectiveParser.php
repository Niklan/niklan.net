<?php declare(strict_types = 1);

namespace Drupal\niklan\Parser\Markdown;

use Drupal\niklan\Helper\CommonMarkDirectiveHelper;
use Drupal\niklan\Node\Markdown\BlockDirective;
use Drupal\niklan\Node\Markdown\ContainerBlockDirective;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockContinueParserWithInlinesInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserEngineInterface;
use League\CommonMark\Util\RegexHelper;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerBlockDirectiveParser extends AbstractBlockContinueParser implements BlockContinueParserWithInlinesInterface {

  /**
   * {@selfdoc}
   */
  private readonly BlockDirective $block;

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly int $colonLength,
    public readonly int $offset,
    public readonly string $directiveInfo,
  ) {
    $info = CommonMarkDirectiveHelper::parseInfoString($this->directiveInfo);
    $attributes = CommonMarkDirectiveHelper::parseExtraAttributes($info['attributes'] ?? '');
    $attributes = CommonMarkDirectiveHelper::flattenExtraAttributes($attributes);

    $this->block = new ContainerBlockDirective(
      type: $info['type'],
      inlineContentRaw: $info['inline-content'],
      argument: $info['argument'],
      attributes: $attributes,
    );
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function getBlock(): AbstractBlock {
    return $this->block;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue {
    if ($cursor->getNextNonSpaceCharacter() === ':') {
      $match = RegexHelper::matchFirst(
        pattern: '/^\s*(:{3,})(?=\s*$)/',
        subject: $cursor->getLine(),
      );

      // Check for closing colon. It should be same length as opening or more.
      if ($match !== NULL && \strlen($match[1]) >= $this->colonLength) {
        return BlockContinue::finished();
      }
    }

    // Only run ::match() if cursor contains space.
    if ($cursor->getNextNonSpacePosition() > $cursor->getPosition()) {
      // Move cursor on the block offset amount. Without this skip of indent
      // offset equals opening indent, it can trigger Indented code parser for
      // depth 3 and above.
      $cursor->match('/^\s{0,' . $this->offset . '}/');
    }

    return BlockContinue::at($cursor);
  }

  /**
   * {@inheritdoc}
   */
  public function canContain(AbstractBlock $childBlock): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isContainer(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function parseInlines(InlineParserEngineInterface $inlineParser): void {
    if (!$this->block->inlineContentRaw) {
      return;
    }

    $inlineParser->parse(
      contents: $this->block->inlineContentRaw,
      block: $this->block->inlineContent,
    );
  }

}
