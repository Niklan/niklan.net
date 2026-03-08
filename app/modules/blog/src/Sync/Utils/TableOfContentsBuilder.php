<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Utils;

use Drupal\Component\Utility\Html;

final class TableOfContentsBuilder {

  /**
   * @return list<array{text: string, anchor: string, indent: int}>
   */
  public function build(string $html): array {
    if (!\str_contains($html, 'heading-permalink')) {
      return [];
    }

    return $this->collectHeadings(Html::load($html));
  }

  /**
   * @return list<array{text: string, anchor: string, indent: int}>
   */
  private function collectHeadings(\DOMDocument $dom): array {
    $xpath = new \DOMXPath($dom);
    $links = $xpath->query('//a[@class="heading-permalink"]');

    if (!$links instanceof \DOMNodeList) {
      return [];
    }

    $headings = [];
    foreach ($links as $link) {
      \assert($link instanceof \DOMElement);
      $heading = $link->parentNode;

      if (!$heading instanceof \DOMElement || !$this->isHeadingTag($heading->tagName)) {
        continue;
      }

      $headings[] = [
        'text' => $this->extractHeadingText($heading),
        'anchor' => $link->getAttribute('href'),
        'indent' => $this->determineIndent($heading->tagName),
      ];
    }

    return $headings;
  }

  private function isHeadingTag(string $tag): bool {
    return \in_array($tag, ['h2', 'h3', 'h4', 'h5', 'h6'], TRUE);
  }

  private function extractHeadingText(\DOMElement $heading): string {
    $text = '';
    foreach ($heading->childNodes as $child) {
      if ($child->nodeType !== \XML_TEXT_NODE) {
        continue;
      }

      $text .= $child->textContent;
    }
    return \trim($text);
  }

  private function determineIndent(string $tag): int {
    return match ($tag) {
      'h2' => 0,
      'h3' => 1,
      'h4' => 2,
      'h5' => 3,
      'h6' => 4,
      default => 0,
    };
  }

}
