<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Parser;

use Drupal\niklan\CommonMark\Block\Note;
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
final class NoteParser extends AbstractBlockContinueParser {

  /**
   * {@selfdoc}
   */
  private readonly Note $block;

  /**
   * {@selfdoc}
   */
  public function __construct(
    private readonly string $type,
  ) {
    $this->block = new Note($this->type);
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
  public function isContainer(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function canContain(AbstractBlock $childBlock): bool {
    return TRUE;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue {
    if (!$cursor->isIndented() && $cursor->getNextNonSpaceCharacter() === '>') {
      $cursor->advanceToNextNonSpaceOrTab();
      $cursor->advanceBy(1);
      $cursor->advanceBySpaceOrTab();

      return BlockContinue::at($cursor);
    }

    return BlockContinue::none();
  }

}
