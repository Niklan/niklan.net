<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure;

use Drupal\Core\Cache\CacheableMetadata;

final class RenderArray {

  /**
   * @var list<\Drupal\external_content\DataStructure\RenderArray> */
  private array $children = [];
  private CacheableMetadata $cacheMetadata;

  public function __construct(private array $element = []) {
    $this->cacheMetadata = new CacheableMetadata();
  }

  public function setProperty(string $key, mixed $value): void {
    $this->element["#$key"] = $value;
  }

  public function addChild(self $child): void {
    $this->children[] = $child;
  }

  public function toRenderArray(): array {
    $build = $this->element;
    foreach ($this->children as $child) {
      $build[] = $child->toRenderArray();
    }
    $this->applyCacheMetadata($build);
    return $build;
  }

  public function applyCacheMetadata(array &$build): void {
    $this->cacheMetadata->applyTo($build);
  }

  public function getElement(): array {
    return $this->element;
  }

  public function getChildren(): array {
    return $this->children;
  }

  public function getCacheMetadata(): CacheableMetadata {
    return $this->cacheMetadata;
  }

}
