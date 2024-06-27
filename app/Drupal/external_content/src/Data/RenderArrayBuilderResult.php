<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final class RenderArrayBuilderResult {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private array $renderArray,
  ) {}

  /**
   * {@selfdoc}
   */
  public static function withRenderArray(array $render_array): self {
    return new self($render_array);
  }

  /**
   * {@selfdoc}
   */
  public static function empty(): self {
    return new self([]);
  }

  /**
   * {@selfdoc}
   */
  public function isBuilt(): bool {
    return (bool) $this->renderArray;
  }

  /**
   * {@selfdoc}
   */
  public function isNotBuild(): bool {
    return !$this->renderArray;
  }

  /**
   * {@selfdoc}
   */
  public function result(): array {
    return $this->renderArray;
  }

}
