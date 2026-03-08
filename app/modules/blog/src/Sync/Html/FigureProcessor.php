<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Html;

use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;

final readonly class FigureProcessor implements HtmlContentProcessor {

  #[\Override]
  public function process(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $this->processFigures($dom);
  }

  private function processFigures(\DOMDocument $dom): void {
    $xpath = new \DOMXPath($dom);
    $figures = $xpath->query('//*[@data-selector="niklan:container-directive"][@data-type="figure"]');

    if (!$figures instanceof \DOMNodeList) {
      return;
    }

    $elements = [];
    foreach ($figures as $node) {
      $elements[] = $node;
    }

    foreach ($elements as $element) {
      \assert($element instanceof \DOMElement);
      $this->processElement($dom, $element);
    }
  }

  private function processElement(\DOMDocument $dom, \DOMElement $element): void {
    $figure = $dom->createElement('figure');

    $this->moveContent($dom, $element, $figure);
    $this->moveFigcaption($dom, $figure);

    $element->parentNode?->replaceChild($figure, $element);
  }

  private function moveContent(\DOMDocument $dom, \DOMElement $source, \DOMElement $figure): void {
    $xpath = new \DOMXPath($dom);
    $content = $xpath->query('./div[@data-selector="content"]', $source);
    $slot = $content instanceof \DOMNodeList ? $content->item(0) : NULL;

    if (!$slot instanceof \DOMElement) {
      return;
    }

    while ($slot->firstChild) {
      $figure->appendChild($slot->firstChild);
    }
  }

  private function moveFigcaption(\DOMDocument $dom, \DOMElement $figure): void {
    $caption_div = $this->findFigcaptionDirective($dom, $figure);
    if (!$caption_div) {
      return;
    }

    $figcaption = $dom->createElement('figcaption');
    $this->moveDirectiveContent($dom, $caption_div, $figcaption);
    $caption_div->parentNode?->replaceChild($figcaption, $caption_div);
  }

  private function findFigcaptionDirective(\DOMDocument $dom, \DOMElement $parent): ?\DOMElement {
    $xpath = new \DOMXPath($dom);
    $captions = $xpath->query('.//*[@data-selector="niklan:container-directive"][@data-type="figcaption"]', $parent);

    if (!$captions instanceof \DOMNodeList || $captions->length === 0) {
      return NULL;
    }

    $node = $captions->item(0);
    \assert($node instanceof \DOMElement);

    return $node;
  }

  private function moveDirectiveContent(\DOMDocument $dom, \DOMElement $source, \DOMElement $target): void {
    $xpath = new \DOMXPath($dom);
    $content = $xpath->query('./div[@data-selector="content"]', $source);
    $slot = $content instanceof \DOMNodeList ? $content->item(0) : NULL;

    if (!$slot instanceof \DOMElement) {
      return;
    }

    while ($slot->firstChild) {
      $target->appendChild($slot->firstChild);
    }
  }

}
