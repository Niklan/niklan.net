<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Parser;

use Drupal\niklan\CommonMark\Block\LeafBlockDirective;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class LeafBlockDirectiveParser extends AbstractBlockContinueParser {

  /**
   * {@selfdoc}
   */
  private readonly LeafBlockDirective $block;

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $containerInfo,
  ) {
    $this->block = new LeafBlockDirective($this->containerInfo);
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function getBlock(): AbstractBlock {
    return $this->block;
  }

  /**
   * {@inheritdoc}
   */
  public function canContain(AbstractBlock $childBlock): bool {
    // Leaf block like an empty div, it can't contain content inside. Only
    // inline content is allowed.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isContainer(): bool {
    return FALSE;
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

}
