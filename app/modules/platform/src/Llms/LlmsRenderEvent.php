<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class LlmsRenderEvent extends Event {

  private ?string $markdown = NULL;
  private ?string $title = NULL;
  private CacheableMetadata $cacheableMetadata;

  public function __construct(
    public readonly array $mainContent,
    public readonly Request $request,
    public readonly RouteMatchInterface $routeMatch,
  ) {
    $this->cacheableMetadata = new CacheableMetadata();
  }

  public function addCacheableDependency(CacheableDependencyInterface $dependency): void {
    $this->cacheableMetadata = $this->cacheableMetadata->merge(CacheableMetadata::createFromObject($dependency));
  }

  public function getCacheableMetadata(): CacheableMetadata {
    return $this->cacheableMetadata;
  }

  public function setMarkdown(string $markdown): void {
    $this->markdown = $markdown;
  }

  public function getMarkdown(): ?string {
    return $this->markdown;
  }

  public function hasCustomMarkdown(): bool {
    return $this->markdown !== NULL;
  }

  public function setTitle(string $title): void {
    $this->title = $title;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

}
