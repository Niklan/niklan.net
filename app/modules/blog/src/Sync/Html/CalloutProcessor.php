<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Html;

use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;

final readonly class CalloutProcessor implements HtmlContentProcessor {

  private const array CALLOUT_TYPES = ['note', 'tip', 'important', 'warning', 'caution'];

  #[\Override]
  public function process(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $xpath = new \DOMXPath($dom);
    $directives = $xpath->query('//*[@data-selector="niklan:container-directive"]');

    if (!$directives) {
      return;
    }

    $elements = [];
    foreach ($directives as $node) {
      $elements[] = $node;
    }

    foreach ($elements as $element) {
      \assert($element instanceof \DOMElement);
      $this->processElement($dom, $element);
    }
  }

  private function processElement(\DOMDocument $dom, \DOMElement $element): void {
    $type = $element->getAttribute('data-type');
    if (!\in_array($type, self::CALLOUT_TYPES, TRUE)) {
      return;
    }

    $callout = $dom->createElement('app-callout');
    $callout->setAttribute('data-type', $type);

    $this->moveSlot($dom, $element, $callout, 'inline-content', 'app-callout-title');
    $this->moveSlot($dom, $element, $callout, 'content', 'app-callout-body');

    $element->parentNode?->replaceChild($callout, $element);
  }

  private function moveSlot(\DOMDocument $dom, \DOMElement $source, \DOMElement $target, string $selector, string $tag_name): void {
    $xpath = new \DOMXPath($dom);
    $result = $xpath->query('./div[@data-selector="' . $selector . '"]', $source);
    $slot = $result instanceof \DOMNodeList ? $result->item(0) : NULL;

    if (!$slot instanceof \DOMElement) {
      return;
    }

    $wrapper = $dom->createElement($tag_name);
    while ($slot->firstChild) {
      $wrapper->appendChild($slot->firstChild);
    }
    $target->appendChild($wrapper);
  }

}
