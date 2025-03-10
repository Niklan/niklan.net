<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Parser;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\NodeList;
use Drupal\niklan\ExternalContent\Node\Alert;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @ingroup external_content
 */
final class AlertParser implements HtmlParserInterface {

  #[\Override]
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult {
    if (!$node instanceof \DOMElement || !$this->isApplicable($node)) {
      return HtmlParserResult::pass();
    }

    $alert = new Alert(
      type: $node->getAttribute('data-type'),
      heading: $this->findHeading($node, $child_parser),
    );

    $content = $this->findContent($node, $child_parser);

    if ($content) {
      $alert->addChildren($content);
    }

    return HtmlParserResult::replace($alert);
  }

  private function isApplicable(\DOMElement $node): bool {
    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:container-directive') {
      return FALSE;
    }

    $allowed_types = ['note', 'tip', 'important', 'warning', 'caution'];
    $type = $node->getAttribute('data-type');

    return \in_array($type, $allowed_types);
  }

  private function findHeading(\DOMNode $node, ChildHtmlParserInterface $child_parser): ?NodeInterface {
    $crawler = new Crawler($node);
    $crawler = $crawler->filter('[data-selector="inline-content"]');

    if (!$crawler->count() || !$crawler->getNode(0) instanceof \DOMNode) {
      return NULL;
    }

    return $child_parser
      ->parse($crawler->getNode(0)->childNodes)
      ->getChildren()
      ->offsetGet(0);
  }

  private function findContent(\DOMNode $node, ChildHtmlParserInterface $child_parser): ?NodeList {
    $crawler = new Crawler($node);
    $crawler = $crawler->filter('[data-selector="content"]');

    if (!$crawler->count() || !$crawler->getNode(0) instanceof \DOMNode) {
      return NULL;
    }

    return $child_parser->parse($crawler->getNode(0)->childNodes);
  }

}
