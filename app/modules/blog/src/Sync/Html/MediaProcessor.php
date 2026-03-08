<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Html;

use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;

final readonly class MediaProcessor implements HtmlContentProcessor {

  public function __construct(
    private MediaSynchronizer $mediaSynchronizer,
  ) {}

  #[\Override]
  public function process(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $this->processImages($dom, $context);
    $this->processVideoDirectives($dom, $context);
    $this->processYouTubeDirectives($dom);
  }

  private function processImages(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $images = $dom->getElementsByTagName('img');
    $elements = [];
    foreach ($images as $img) {
      $elements[] = $img;
    }

    foreach ($elements as $element) {
      $this->processImage($dom, $element, $context);
    }
  }

  private function processImage(\DOMDocument $dom, \DOMElement $img, ArticleProcessingContext $context): void {
    $src = $img->getAttribute('src');
    if (!$src) {
      return;
    }

    $asset_path = $context->translation->contentDirectory . '/' . $src;
    $media = $this->mediaSynchronizer->sync($asset_path);
    if (!$media?->uuid()) {
      $img->parentNode?->removeChild($img);
      return;
    }

    $placeholder = $dom->createElement('app-media');
    $placeholder->setAttribute('data-uuid', $media->uuid());
    $placeholder->setAttribute('data-bundle', 'image');

    $alt = $img->getAttribute('alt');
    if ($alt) {
      $placeholder->setAttribute('data-alt', $alt);
    }
    $placeholder->setAttribute('data-src', $src);

    $img->parentNode?->replaceChild($placeholder, $img);
  }

  private function processVideoDirectives(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $elements = $this->queryLeafDirectives($dom, 'video');
    foreach ($elements as $element) {
      $this->processVideoDirective($dom, $element, $context);
    }
  }

  private function processVideoDirective(\DOMDocument $dom, \DOMElement $element, ArticleProcessingContext $context): void {
    $src = $element->getAttribute('data-argument');
    if (!$src) {
      $element->parentNode?->removeChild($element);
      return;
    }

    $asset_path = $context->translation->contentDirectory . '/' . $src;
    $media = $this->mediaSynchronizer->sync($asset_path);
    if (!$media?->uuid()) {
      $element->parentNode?->removeChild($element);
      return;
    }

    $placeholder = $dom->createElement('app-media');
    $placeholder->setAttribute('data-uuid', $media->uuid());
    $placeholder->setAttribute('data-bundle', 'video');

    $title = $this->extractInlineContent($dom, $element);
    if ($title) {
      $placeholder->setAttribute('data-title', $title);
    }

    $this->copyVideoAttributes($element, $placeholder);

    $element->parentNode?->replaceChild($placeholder, $element);
  }

  private function processYouTubeDirectives(\DOMDocument $dom): void {
    $elements = $this->queryLeafDirectives($dom, 'youtube');
    foreach ($elements as $element) {
      $this->processYouTubeDirective($dom, $element);
    }
  }

  private function processYouTubeDirective(\DOMDocument $dom, \DOMElement $element): void {
    $vid = $element->getAttribute('vid');
    if (!$vid) {
      $element->parentNode?->removeChild($element);
      return;
    }

    $media = $this->mediaSynchronizer->sync('https://youtu.be/' . $vid);
    if (!$media?->uuid()) {
      $element->parentNode?->removeChild($element);
      return;
    }

    $placeholder = $dom->createElement('app-media');
    $placeholder->setAttribute('data-uuid', $media->uuid());
    $placeholder->setAttribute('data-bundle', 'remote_video');

    $element->parentNode?->replaceChild($placeholder, $element);
  }

  /**
   * @return list<\DOMElement>
   */
  private function queryLeafDirectives(\DOMDocument $dom, string $type): array {
    $xpath = new \DOMXPath($dom);
    $nodes = $xpath->query('//*[@data-selector="niklan:leaf-directive"][@data-type="' . $type . '"]');

    if (!$nodes instanceof \DOMNodeList) {
      return [];
    }

    $elements = [];
    foreach ($nodes as $node) {
      \assert($node instanceof \DOMElement);
      $elements[] = $node;
    }

    return $elements;
  }

  private function copyVideoAttributes(\DOMElement $source, \DOMElement $target): void {
    foreach (['muted', 'autoplay', 'loop', 'controls'] as $attr) {
      if (!$source->hasAttribute($attr)) {
        continue;
      }

      $target->setAttribute($attr, '');
    }
  }

  private function extractInlineContent(\DOMDocument $dom, \DOMElement $element): ?string {
    $xpath = new \DOMXPath($dom);
    $result = $xpath->query('.//div[@data-selector="inline-content"]', $element);
    $inline = $result instanceof \DOMNodeList ? $result->item(0) : NULL;

    return $inline instanceof \DOMElement
        ? ($inline->textContent ?: NULL)
        : NULL;
  }

}
