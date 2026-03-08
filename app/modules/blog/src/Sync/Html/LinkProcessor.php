<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Html;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Site\Settings;
use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_contract\Utils\PathHelper;

final readonly class LinkProcessor implements HtmlContentProcessor {

  #[\Override]
  public function process(\DOMDocument $dom, ArticleProcessingContext $context): void {
    $links = $dom->getElementsByTagName('a');

    $elements = [];
    foreach ($links as $link) {
      $elements[] = $link;
    }

    foreach ($elements as $element) {
      $this->processLink($element, $context);
    }
  }

  private function processLink(\DOMElement $element, ArticleProcessingContext $context): void {
    $href = $element->getAttribute('href');
    if (!$href || UrlHelper::isExternal($href) || \str_starts_with($href, '#')) {
      return;
    }

    $full_path = PathHelper::normalizePath(
      $context->translation->contentDirectory . \DIRECTORY_SEPARATOR . $href,
    );

    $this->updateLinkByPathType($element, $full_path, $context);
  }

  private function updateLinkByPathType(\DOMElement $element, string $full_path, ArticleProcessingContext $context): void {
    if (\is_dir($full_path)) {
      $this->convertToRepositoryLink($element, $full_path);
    }
    elseif (\is_file($full_path)) {
      $this->markAsInternalLink($element, $full_path, $context);
    }
  }

  private function convertToRepositoryLink(\DOMElement $element, string $path): void {
    $content_dir = Settings::get('content_directory');
    $repository_url = Settings::get('content_repository_url');
    \assert(\is_string($content_dir) && \is_string($repository_url));

    $element->setAttribute('href', \str_replace(
      search: $content_dir,
      replace: "$repository_url/tree/main",
      subject: $path,
    ));
  }

  private function markAsInternalLink(\DOMElement $element, string $path, ArticleProcessingContext $context): void {
    $element->removeAttribute('href');
    $element->setAttribute(
      'data-source-path-hash',
      PathHelper::hashRelativePath(path: $path, base_path: $context->contentRoot),
    );
  }

}
