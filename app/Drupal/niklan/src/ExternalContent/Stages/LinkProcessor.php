<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Site\Settings;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Nodes\HtmlElement\HtmlElement;
use Drupal\external_content\Nodes\Node;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\Utils\PathHelper;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
final readonly class LinkProcessor implements PipelineStage {

  public function process(PipelineContext $context): void {
    $this->traverseNodeHierarchy($context->externalContent, $context);
  }

  private function traverseNodeHierarchy(Node $node, ArticleTranslationProcessContext $context): void {
    foreach ($node->getChildren() as $child) {
      $this->traverseNodeHierarchy($child, $context);
    }
    $this->processLinkNode($node, $context);
  }

  private function processLinkNode(Node $node, ArticleTranslationProcessContext $context): void {
    if (!$this->isValidLinkElement($node)) {
      return;
    }

    $link_url = $node->attributes['href'];
    if ($this->shouldSkipProcessing($link_url)) {
      return;
    }

    $full_path = $this->buildFullPath($link_url, $context);
    $this->updateLinkBasedOnPathType($node, $full_path);
  }

  private function isValidLinkElement(Node $node): bool {
    return $node instanceof HtmlElement
      && $node->tag === 'a'
      && isset($node->attributes['href']);
  }

  private function shouldSkipProcessing(string $url): bool {
    return UrlHelper::isExternal($url) || \str_starts_with($url, '#');
  }

  private function buildFullPath(string $relative_path, ArticleTranslationProcessContext $context): string {
    $relativePathname = $context->articleTranslation->contentDirectory . \DIRECTORY_SEPARATOR . $relative_path;
    return PathHelper::normalizePath($relativePathname);
  }

  private function updateLinkBasedOnPathType(HtmlElement $node, string $full_path): void {
    match (TRUE) {
      \is_dir($full_path) => $this->convertToRepositoryLink($node, $full_path),
      \is_file($full_path) => $this->markAsInternalArticleLink($node, $full_path),
      default => NULL,
    };
  }

  private function convertToRepositoryLink(HtmlElement $node, string $path): void {
    $external_content_dir = Settings::get('external_content_directory');
    $repository_url = Settings::get('external_content_repository_url');

    \assert(\is_string($external_content_dir) && \is_string($repository_url), 'Settings must be properly configured');

    $node->attributes['href'] = \str_replace(
      search: $external_content_dir,
      // GitHub requires '/tree/main' path part.
      replace: "$repository_url/tree/main",
      subject: $path,
    );
  }

  private function markAsInternalArticleLink(HtmlElement $node, string $path): void {
    unset($node->attributes['href']);
    $node->attributes['data-source-path-hash'] = \md5($path);
  }

}
