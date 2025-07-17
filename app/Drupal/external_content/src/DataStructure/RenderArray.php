<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure;

use Drupal\Core\Cache\CacheableMetadata;

final class RenderArray {

  /**
   * @var list<\Drupal\external_content\DataStructure\RenderArray> */
  private array $children = [];

  public function __construct(
    private array $element = [],
    public CacheableMetadata $cacheableMetadata = new CacheableMetadata(),
  ) {}

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
    $this->cacheableMetadata->applyTo($build);
    return $build;
  }

  public function getElement(): array {
    return $this->element;
  }

  public function getChildren(): array {
    return $this->children;
  }

}
