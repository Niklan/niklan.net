<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final class RenderArrayBuilderResult {

  public function __construct(
    private array $renderArray,
  ) {}

  public static function withRenderArray(array $render_array): self {
    return new self($render_array);
  }

  public static function empty(): self {
    return new self([]);
  }

  public function isBuilt(): bool {
    return (bool) $this->renderArray;
  }

  public function isNotBuild(): bool {
    return !$this->renderArray;
  }

  public function result(): array {
    return $this->renderArray;
  }

}
