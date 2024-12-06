<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Structure;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\RefinableCacheableDependencyInterface;

final class SiteMap extends Element implements RefinableCacheableDependencyInterface {

  private readonly CacheableMetadata $cacheableMetadata;

  public function __construct() {
    $this->cacheableMetadata = new CacheableMetadata();
  }

  public function add(Category $category): void {
    $this->collection[] = $category;
  }

  public function mergeSiteMap(self $sitemap): self {
    foreach ($sitemap as $category) {
      $this->add($category);
    }

    return $this;
  }

  #[\Override]
  public function getCacheContexts(): array {
    return $this->cacheableMetadata->getCacheContexts();
  }

  #[\Override]
  public function getCacheTags(): array {
    return $this->cacheableMetadata->getCacheTags();
  }

  #[\Override]
  public function getCacheMaxAge(): int {
    return $this->cacheableMetadata->getCacheMaxAge();
  }

  #[\Override]
  public function addCacheContexts(array $cache_contexts): self {
    $this->cacheableMetadata->addCacheContexts($cache_contexts);

    return $this;
  }

  #[\Override]
  public function addCacheTags(array $cache_tags): self {
    $this->cacheableMetadata->addCacheTags($cache_tags);

    return $this;
  }

  #[\Override]
  public function mergeCacheMaxAge($max_age): self {
    $this->cacheableMetadata->mergeCacheMaxAge($max_age);

    return $this;
  }

  #[\Override]
  public function addCacheableDependency($other_object): self {
    $this->cacheableMetadata->addCacheableDependency($other_object);

    return $this;
  }

  #[\Override]
  public function toArray(): array {
    return \array_map(static fn (Category $category): array => $category->toArray(), $this->collection);
  }

}
