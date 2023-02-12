<?php

declare(strict_types = 1);

namespace Drupal\external_content\Dto;

use Drupal\Component\Utility\NestedArray;

/**
 * Provides storage for source content parameters.
 *
 * These parameters parsed from Front Matter and stored in this value object.
 */
final class SourceFileParams {

  /**
   * Constructs a new SourceFileParamsTest object.
   *
   * @param array $params
   *   The array with params values.
   */
  public function __construct(
    protected array $params,
  ) {}

  /**
   * Gets all params values.
   *
   * @return array
   *   The params values.
   */
  public function all(): array {
    return $this->params;
  }

  /**
   * Checks if params value is exists.
   *
   * @param string $key
   *   The array key. Supports "depth", see ::getParents().
   *
   * @return bool
   *   TRUE if keys is existing, FALSE otherwise.
   */
  public function has(string $key): bool {
    return NestedArray::keyExists($this->params, $this->getParents($key));
  }

  /**
   * Prepares parents for NestedArray.
   *
   * @param string $key
   *   The array key. Can contain . (dot) to provide "depth". E.g. "foo.bar"
   *   will be searched as $arr['foo']['bar'].
   *
   * @return array
   *   The array with keys.
   */
  protected function getParents(string $key): array {
    return \explode('.', $key);
  }

  /**
   * Gets params value.
   *
   * @param string $key
   *   The array key. Supports "depth", see ::getParents().
   *
   * @return mixed
   *   The params value. Can be NULL if value is actually NULL or doesn't
   *   exists.
   */
  public function get(string $key): mixed {
    return NestedArray::getValue($this->params, $this->getParents($key));
  }

}
