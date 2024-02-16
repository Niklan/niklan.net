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
 * Example:
 * @code
 * ::: !name [inline-content] {#foo .bar key=val key=val}
 * Note contents.
 * :::
 * @endcode
 *
 * Currently inline content and metadata is not supported.
 *
 * @todo Add inline content and metadata support when nothing to do.
 *
 * @ingroup markdown
 */
final class ContainerDirectiveStartParser implements BlockStartParserInterface {

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart {
    if ($cursor->isIndented() || $cursor->getNextNonSpaceCharacter() !== ':') {
      return BlockStart::none();
    }

    // Check for ':::'.
    // @todo Add support for 3+ ':' which can be used for nesting since it is
    //   a container.
    if ($cursor->match('/^\s*(:{3})/') === NULL) {
      return BlockStart::none();
    }

    // Check for '!type' of container which should follow opening ':::'.
    // This is important to check, otherwise closing tag will be captured as
    // well. Another important thing here is that it's just a regex, not a
    // $cursor->match() which moves the cursor on the matched length. The
    // information starting from type should be passed to the block for further
    // parsing of metadata.
    $match = RegexHelper::matchFirst(
      pattern: '/^\s*[a-z]+/',
      subject: $cursor->getRemainder(),
    );

    if (!$match) {
      return BlockStart::none();
    }

    // Get container info.
    $container_info = \trim($cursor->getRemainder());
    // Move cursor at the end to skip parsing that info.
    $cursor->advanceToEnd();

    return BlockStart::of(new ContainerDirectiveParser($container_info))->at($cursor);
  }

}
