<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Utils;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Node\StringContainerInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;

final class TocBuilder {

  public function getTree(ExternalContentFieldItem $item): array {
    $headings = $this->getHeadings($item);

    if (!\count($headings)) {
      return [];
    }

    return $headings;
  }

  protected function getHeadings(ExternalContentFieldItem $item): array {
    $content = $item->get('content')->getValue();
    \assert($content instanceof Content);
    $headings = [];
    $this->recursiveGetHeadings($content, $headings);

    return $headings;
  }

  protected function recursiveGetHeadings(NodeInterface $content, array &$headings): void {
    foreach ($content->getChildren() as $child) {
      \assert($child instanceof NodeInterface);
      $this->processNode($child, $headings);
    }
  }

  protected function processNode(NodeInterface $node, array &$headings): void {
    if ($node->hasChildren()) {
      $this->recursiveGetHeadings($node, $headings);
    }

    if (!($node instanceof Element) || $node->getTag() !== 'a' || $node->getAttributes()->getAttribute('class') !== 'heading-permalink') {
      return;
    }

    $this->processPermalink($node, $headings);
  }

  protected function processPermalink(Element $link, array &$headings): void {
    $heading = $link->getParent();
    \assert($heading instanceof Element);
    $content = '';

    foreach ($heading->getChildren() as $child) {
      if (!($child instanceof StringContainerInterface)) {
        continue;
      }

      $content .= $child->getLiteral();
    }

    $headings[] = [
      'text' => $content,
      'anchor' => $link->getAttributes()->getAttribute('href'),
      'indent' => match ($heading->getTag()) {
        default => 0,
        'h3' => 1,
        'h4' => 2,
        'h5' => 3,
        'h6' => 4,
      },
    ];
  }

}
