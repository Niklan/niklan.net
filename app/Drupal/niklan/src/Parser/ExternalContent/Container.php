<?php

declare(strict_types=1);

namespace Drupal\niklan\Parser\ExternalContent;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\NodeList;
use Symfony\Component\DomCrawler\Crawler;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final class Container implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult {
    if (!$node instanceof \DOMElement || !$this->isApplicable($node)) {
      return HtmlParserResult::pass();
    }

    $content = $this->findContent($node, $child_parser);

    if (!$content) {
      return HtmlParserResult::pass();
    }

    $element = new Element($node->getAttribute('data-type'));
    $element->addChildren($content);

    return HtmlParserResult::replace($element);
  }

  /**
   * {@selfdoc}
   */
  private function isApplicable(\DOMElement $node): bool {
    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:container-directive') {
      return FALSE;
    }

    $allowed_types = ['figure', 'figcaption'];

    return \in_array($node->getAttribute('data-type'), $allowed_types);
  }

  /**
   * {@selfdoc}
   */
  private function findContent(\DOMNode $node, ChildHtmlParserInterface $child_parser): ?NodeList {
    $crawler = new Crawler($node);
    $crawler = $crawler->filter('[data-selector="content"]');

    if (!$crawler->count()) {
      return NULL;
    }

    return $child_parser->parse($crawler->getNode(0)->childNodes);
  }

}
