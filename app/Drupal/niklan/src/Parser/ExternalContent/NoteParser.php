<?php declare(strict_types = 1);

namespace Drupal\niklan\Parser\ExternalContent;

use Drupal\external_content\Contract\Parser\Html\HtmlParserInterface;
use Drupal\external_content\Contract\Parser\Html\HtmlParserResultInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\niklan\Node\ExternalContent\Note;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final class NoteParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node): HtmlParserResultInterface {
    if (!$node instanceof \DOMElement) {
      return HtmlParserResult::continue();
    }

    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:container-directive') {
      return HtmlParserResult::continue();
    }

    $allowed_types = ['note', 'tip', 'important', 'warning', 'caution'];
    $type = $node->getAttribute('data-type');

    if (!\in_array($type, $allowed_types)) {
      return HtmlParserResult::continue();
    }

    // @todo Parse inline content to use as a title.
    return HtmlParserResult::replace(new Note($type));
  }

}
