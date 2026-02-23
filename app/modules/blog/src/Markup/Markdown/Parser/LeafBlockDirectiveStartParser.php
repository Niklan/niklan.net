<?php

declare(strict_types=1);

namespace Drupal\app_blog\Markup\Markdown\Parser;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Util\RegexHelper;

/**
 * @see \Drupal\app_blog\Markup\Markdown\Extension\LeafBlockDirectiveExtension
 *
 * @ingroup markdown
 */
final class LeafBlockDirectiveStartParser implements BlockStartParserInterface {

  #[\Override]
  public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart {
    if ($cursor->getNextNonSpaceCharacter() !== ':') {
      return BlockStart::none();
    }

    // Check for '::'.
    $colon = $cursor->match('/^\s*(:{2})/');

    if ($colon === NULL) {
      return BlockStart::none();
    }

    $type_match = RegexHelper::matchFirst(
      pattern: '/^\s*[a-z]+/',
      subject: $cursor->getRemainder(),
    );

    if ($type_match === NULL) {
      return BlockStart::none();
    }

    $directive_info = \trim($cursor->getRemainder());
    // Move cursor at the end to skip parsing that info.
    $cursor->advanceToEnd();
    $parser = new LeafBlockDirectiveParser($directive_info);

    return BlockStart::of($parser)->at($cursor);
  }

}
