<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Parser;

use Drupal\niklan\CommonMark\Block\ContainerBlockDirective;
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
final class ContainerBlockDirectiveParser extends AbstractBlockContinueParser {

  /**
   * {@selfdoc}
   */
  private readonly ContainerBlockDirective $block;

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly int $colonLength,
    public readonly int $offset,
    public readonly string $containerInfo,
  ) {
    $this->block = new ContainerBlockDirective(
      colonLength: $this->colonLength,
      offset: $this->offset,
      info: $this->containerInfo,
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
    // Make sure to get all block information from the active one, not from
    // $this.
    $active_block = $activeBlockParser->getBlock();
    \assert($active_block instanceof ContainerBlockDirective);

    if ($cursor->getNextNonSpaceCharacter() === ':') {
      $match = RegexHelper::matchFirst(
        pattern: '/^\s*(:{3,})(?=\s*$)/',
        subject: $cursor->getLine(),
      );

      // Check for closing colon. It should be same length as opening or more.
      if ($match !== NULL && \strlen($match[0]) >= $active_block->colonLength) {
        return BlockContinue::finished();
      }
    }

    // Only run ::match() if cursor contains space.
    if ($cursor->getNextNonSpacePosition() > $cursor->getPosition()) {
      // Move cursor on the block offset amount. Without this skip of indent
      // offset equals opening indent, it can trigger Indented code parser for
      // depth 3 and above.
      $cursor->match('/^\s{0,' . $active_block->offset . '}/');
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
