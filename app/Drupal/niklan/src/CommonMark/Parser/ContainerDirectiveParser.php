<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Parser;

use Drupal\niklan\CommonMark\Block\ContainerDirective;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerDirectiveParser extends AbstractBlockContinueParser {

  /**
   * {@selfdoc}
   */
  private readonly ContainerDirective $block;

  /**
   * {@selfdoc}
   */
  private string $content = '';

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $containerInfo,
  ) {
    $this->block = new ContainerDirective($this->containerInfo);
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
    return TRUE;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue {
    if (!$cursor->isIndented() && $cursor->getNextNonSpaceCharacter() === ':') {
      $match = RegexHelper::matchFirst(
        pattern: '/^(:{3})(?=\s*$)/',
        subject: $cursor->getLine(),
      );

      // Closing ':::' found.
      if ($match !== NULL) {
        return BlockContinue::finished();
      }
    }

    return BlockContinue::at($cursor);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function isContainer(): bool {
    return TRUE;
  }

}
