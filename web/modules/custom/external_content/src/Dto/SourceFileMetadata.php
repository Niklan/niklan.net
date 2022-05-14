<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

use Drupal\Component\Utility\NestedArray;

/**
 * Provides storage for source content metadata.
 *
 * This metadata parsed from Front Matter and stored in this value object.
 */
final class SourceFileMetadata {

  /**
   * Constructs a new SourceMetadataTest object.
   *
   * @param array $metadata
   *   The array with metadata values.
   */
  public function __construct(
    protected array $metadata,
  ) {}

  /**
   * Gets all metadata values.
   *
   * @return array
   *   The metadata values.
   */
  public function all(): array {
    return $this->metadata;
  }

  /**
   * Checks if metadata value is exists.
   *
   * @param string $key
   *   The array key. Supports "depth", see ::getParents().
   *
   * @return bool
   *   TRUE if keys is existing, FALSE otherwise.
   */
  public function has(string $key): bool {
    return NestedArray::keyExists($this->metadata, $this->getParents($key));
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
   * Gets metadata value.
   *
   * @param string $key
   *   The array key. Supports "depth", see ::getParents().
   *
   * @return mixed
   *   The metadata value. Can be NULL if value is actually NULL or doesn't
   *   exists.
   */
  public function get(string $key): mixed {
    return NestedArray::getValue($this->metadata, $this->getParents($key));
  }

}
