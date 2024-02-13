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

    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:note') {
      return HtmlParserResult::continue();
    }

    $note_type = $node->getAttribute('data-note-type');

    return HtmlParserResult::replace(new Note($note_type));
  }

}
