<?php declare(strict_types = 1);

namespace Drupal\niklan\Parser\ExternalContent;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\niklan\Node\ExternalContent\RemoteVideo as RemoteVideoNode;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final class RemoteVideo implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult {
    if (!$node instanceof \DOMElement || !$this->isApplicable($node)) {
      return HtmlParserResult::pass();
    }

    \assert($node instanceof \DOMElement);
    $vid = $node->getAttribute('data-vid');
    $node = new RemoteVideoNode("https://youtu.be/{$vid}");

    return HtmlParserResult::replace($node);
  }

  /**
   * {@selfdoc}
   */
  private function isApplicable(\DOMElement $node): bool {
    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($node->getAttribute('data-type') !== 'youtube') {
      return FALSE;
    }

    return $node->hasAttribute('data-vid');
  }

}
