<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Parser;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\niklan\ExternalContent\Node\Video as VideoNode;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @ingroup external_content
 */
final class Video implements HtmlParserInterface {

  #[\Override]
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult {
    if (!$node instanceof \DOMElement || !$this->isApplicable($node)) {
      return HtmlParserResult::pass();
    }

    $title = $this->findTitle($node);

    if (!$title) {
      return HtmlParserResult::pass();
    }

    $node = new VideoNode(
      src: $node->getAttribute('data-argument'),
      title: $title,
    );

    return HtmlParserResult::replace($node);
  }

  private function isApplicable(\DOMElement $node): bool {
    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($node->getAttribute('data-type') !== 'video') {
      return FALSE;
    }

    return $node->hasAttribute('data-argument');
  }

  private function findTitle(\DOMNode $node): ?string {
    $crawler = new Crawler($node);
    $crawler = $crawler->filter('[data-selector="inline-content"]');

    if (!$crawler->count()) {
      return NULL;
    }

    return $crawler->text();
  }

}
