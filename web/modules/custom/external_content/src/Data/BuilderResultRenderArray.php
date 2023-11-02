<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a builder result with a render array.
 */
final class BuilderResultRenderArray extends BuilderResult {

  /**
   * Constructs a new BuilderResultRenderArray instance.
   */
  public function __construct(
    protected array $renderArray,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isBuilt(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNotBuild(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function result(): array {
    return $this->renderArray;
  }

}
