<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Html;

use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;

final readonly class CodeBlockProcessor implements HtmlContentProcessor {

  #[\Override]
  public function process(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $pre_elements = $dom->getElementsByTagName('pre');

    $elements = [];
    foreach ($pre_elements as $pre) {
      if ($pre->firstChild?->nodeName !== 'code') {
        continue;
      }

      $elements[] = $pre;
    }

    foreach ($elements as $element) {
      $this->processElement($dom, $element);
    }
  }

  private function processElement(\DOMDocument $dom, \DOMElement $pre): void {
    $code = $pre->firstChild;
    \assert($code instanceof \DOMElement);

    $placeholder = $dom->createElement('app-code-block');
    $placeholder->textContent = $code->textContent;

    $this->setOptionalAttribute($placeholder, 'data-language', $this->extractLanguage($pre, $code));
    $this->applyInfoAttributes($pre, $placeholder);

    $pre->parentNode?->replaceChild($placeholder, $pre);
  }

  private function extractLanguage(\DOMElement $pre, \DOMElement $code): ?string {
    if ($pre->hasAttribute('data-language')) {
      return $pre->getAttribute('data-language');
    }

    $class = $code->getAttribute('class');
    if (\preg_match('/language-(\S+)/', $class, $matches)) {
      return $matches[1];
    }

    return NULL;
  }

  private function applyInfoAttributes(\DOMElement $pre, \DOMElement $placeholder): void {
    $info_json = $pre->getAttribute('data-info');
    if (!$info_json) {
      return;
    }

    $info = \json_decode($info_json);
    if (!$info instanceof \stdClass) {
      return;
    }

    $this->setOptionalAttribute($placeholder, 'data-highlighted-lines', $info->highlighted_lines ?? NULL);
    $this->setOptionalAttribute($placeholder, 'data-header', $info->header ?? NULL);
  }

  private function setOptionalAttribute(\DOMElement $element, string $name, ?string $value): void {
    if ($value === NULL || $value === '') {
      return;
    }

    $element->setAttribute($name, $value);
  }

}
