<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Builder;

use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Nodes\Heading\Heading;
use Drupal\external_content\Nodes\HtmlElement\HtmlElement;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Nodes\Text\Text;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;

final class TableOfContentsBuilder {

  /**
   * @return array<array{text: string, anchor: string, indent: int}>
   */
  public function build(ExternalContentFieldItem $item): array {
    $document = $item->get('content')->getValue();
    return $document instanceof Document
      ? $this->collectHeadings($document)
      : [];
  }

  /**
   * @return array<array{text: string, anchor: string, indent: int}>
   */
  private function collectHeadings(Document $document): array {
    $headings = [];
    $this->traverseNodes($document, $headings);
    return $headings;
  }

  /**
   * @param array<array{text: string, anchor: string, indent: int}> $headings
   */
  private function traverseNodes(Node $node, array &$headings): void {
    foreach ($node->getChildren() as $child) {
      if ($this->isHeadingAnchorLink($child)) {
        $this->addHeadingEntry($child, $headings);
        continue;
      }

      $this->processNestedNodes($child, $headings);
    }
  }

  /**
   * @phpstan-assert-if-true \Drupal\external_content\Nodes\HtmlElement\HtmlElement $node
   */
  private function isHeadingAnchorLink(Node $node): bool {
    return $node instanceof HtmlElement
      && $node->tag === 'a'
      && ($node->attributes['class'] ?? '') === 'heading-permalink'
      && $node->getParent() instanceof Heading;
  }

  /**
   * @param array<array{text: string, anchor: string, indent: int}> $headings
   */
  private function addHeadingEntry(HtmlElement $link, array &$headings): void {
    $heading = $link->getParent();
    \assert($heading instanceof Heading);

    $headings[] = [
      'text' => $this->extractHeadingTextContent($heading),
      'anchor' => (string) ($link->attributes['href'] ?? ''),
      'indent' => $this->determineHeadingLevel($heading->tag->value),
    ];
  }

  private function extractHeadingTextContent(Heading $heading): string {
    $textContent = '';
    foreach ($heading->getChildren() as $child) {
      if (!($child instanceof Text)) {
        continue;
      }

      $textContent .= $child->text;
    }
    return $textContent;
  }

  private function determineHeadingLevel(string $tag): int {
    return match ($tag) {
      'h2' => 0,
      'h3' => 1,
      'h4' => 2,
      'h5' => 3,
      'h6' => 4,
      default => throw new \InvalidArgumentException("Unsupported heading tag: $tag"),
    };
  }

  /**
   * @param array<array{text: string, anchor: string, indent: int}> $headings
   */
  private function processNestedNodes(Node $node, array &$headings): void {
    if (!$node->hasChildren()) {
      return;
    }

    $this->traverseNodes($node, $headings);
  }

}
