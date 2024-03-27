<?php declare(strict_types = 1);

namespace Drupal\niklan\Parser\ExternalContent;

use Drupal\external_content\Contract\Parser\Html\HtmlParserResultInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\niklan\Node\ExternalContent\Note;
use Symfony\Component\DomCrawler\Crawler;

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
    if (!$this->isApplicable($node)) {
      return HtmlParserResult::continue();
    }

    $type = $node->getAttribute('data-type');
    $heading = $this->findHeader($node);

    return HtmlParserResult::replace(new Note($type, $heading));
  }

  /**
   * {@selfdoc}
   */
  private function isApplicable(\DOMNode $node): bool {
    if (!$node instanceof \DOMElement) {
      return FALSE;
    }

    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:container-directive') {
      return FALSE;
    }

    $allowed_types = ['note', 'tip', 'important', 'warning', 'caution'];
    $type = $node->getAttribute('data-type');

    if (!\in_array($type, $allowed_types)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@selfdoc}
   */
  private function findHeader(\DOMNode $node) {
    $crawler = new Crawler($node);
    $crawler = $crawler->filter('[data-selector="inline-content"]');

    if (!$crawler->count()) {
      return NULL;
    }

    $node = $crawler->getNode(0);
    $heading = '';

    foreach ($node->childNodes as $node) {
      \assert($node instanceof \DOMNode);
      $heading .= $node->ownerDocument->saveHTML($node);
    }

    return $heading;
  }

}
