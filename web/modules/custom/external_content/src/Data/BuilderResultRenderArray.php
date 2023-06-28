<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Builder\BuilderResultRenderArrayInterface;

/**
 * Represents a builder result with a render array.
 */
final class BuilderResultRenderArray extends BuilderResult implements BuilderResultRenderArrayInterface {

  /**
   * Constructs a new BuilderResultRenderArray instance.
   *
   * @param array $renderArray
   *   The render array result.
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
  public function getRenderArray(): array {
    return $this->renderArray;
  }

}
