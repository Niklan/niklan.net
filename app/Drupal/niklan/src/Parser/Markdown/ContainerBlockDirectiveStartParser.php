<?php

declare(strict_types=1);

namespace Drupal\niklan\Parser\Markdown;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Util\RegexHelper;

/**
 * {@selfdoc}
 *
 * @see \Drupal\niklan\Extension\Markdown\ContainerBlockDirectiveExtension
 *
 * @ingroup markdown
 */
final class ContainerBlockDirectiveStartParser implements BlockStartParserInterface {

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart {
    if ($cursor->getNextNonSpaceCharacter() !== ':') {
      return BlockStart::none();
    }

    // Check for ':::+'.
    $colon = $cursor->match('/^\s*(:{3,})/');

    if ($colon === NULL) {
      return BlockStart::none();
    }

    // Check for 'type' of container which should follow opening ':::'.
    // This is important to check, otherwise closing tag will be captured as
    // well. Another important thing here is that it's just a regex, not a
    // $cursor->match() which moves the cursor on the matched length. The
    // information starting from type should be passed to the block for further
    // parsing of metadata.
    $type_match = RegexHelper::matchFirst(
      pattern: '/^\s*[a-z]+/',
      subject: $cursor->getRemainder(),
    );

    if ($type_match === NULL) {
      return BlockStart::none();
    }

    $directiveInfo = \trim($cursor->getRemainder());
    $cursor->advanceToEnd();

    $parser = new ContainerBlockDirectiveParser(
    // Make sure to clear up content before colon to calculate a proper length
    // of opening 'tag'.
      colonLength: \strlen(\ltrim($colon, " \t")),
      offset: $cursor->getIndent(),
      directiveInfo: $directiveInfo,
    );

    return BlockStart::of($parser)->at($cursor);
  }

}
