<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Parser;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Util\RegexHelper;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class NoteStartParser implements BlockStartParserInterface {

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart {
    if ($cursor->isIndented()) {
      return BlockStart::none();
    }

    // It uses blockquote syntax.
    if ($cursor->getNextNonSpaceCharacter() !== '>') {
      return BlockStart::none();
    }

    $cursor->advanceToNextNonSpaceOrTab();
    $cursor->advanceBy(1);
    $cursor->advanceBySpaceOrTab();

    $match = RegexHelper::matchFirst(
      pattern: '/\[\!(NOTE|TIP|IMPORTANT|WARNING|CAUTION)\]/',
      subject: $cursor->getRemainder(),
    );

    if (!$match) {
      return BlockStart::none();
    }

    // Skip first line with '[!TYPE]' construction. It shouldn't be the part of
    // note content.
    $cursor->advanceToEnd();

    return BlockStart::of(new NoteParser($match[1]))->at($cursor);
  }

}
