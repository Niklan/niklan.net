<?php declare(strict_types = 1);

namespace Drupal\niklan\Parser\Markdown;

use Drupal\niklan\Helper\CommonMarkDirectiveHelper;
use Drupal\niklan\Node\Markdown\BlockDirective;
use Drupal\niklan\Node\Markdown\LeafBlockDirective;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockContinueParserWithInlinesInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserEngineInterface;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class LeafBlockDirectiveParser extends AbstractBlockContinueParser implements BlockContinueParserWithInlinesInterface {

  /**
   * {@selfdoc}
   */
  private readonly BlockDirective $block;

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $directiveInfo,
  ) {
    $info = CommonMarkDirectiveHelper::parseInfoString($this->directiveInfo);
    $attributes = CommonMarkDirectiveHelper::parseExtraAttributes($info['attributes'] ?? '');

    $this->block = new LeafBlockDirective(
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
    // Make sure we at the end for the cursor line. It is moved here at start
    // parser, but still, make sure this is the case.
    if ($cursor->isAtEnd()) {
      return BlockContinue::finished();
    }

    $cursor->advanceBy(1);

    return BlockContinue::at($cursor);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isContainer(): bool {
    return FALSE;
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
