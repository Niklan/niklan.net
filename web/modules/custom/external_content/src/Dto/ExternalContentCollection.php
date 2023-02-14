<?php declare(strict_types = 1);

namespace Drupal\external_content\Dto;

/**
 * Represents a collection of external content items.
 */
final class ExternalContentCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with parsed source file items.
   *
   * @var \Drupal\external_content\Dto\ExternalContent[]
   */
  protected array $items = [];

  /**
   * Adds a parsed source file into collection.
   *
   * @param \Drupal\external_content\Dto\ExternalContent $file
   *   The external content.
   */
  public function add(ExternalContent $file): void {
    $this->items[$file->id()] = $file;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->items);
  }

  /**
   * Gets content by its ID.
   *
   * @param string $id
   *   The content ID.
   *
   * @return \Drupal\external_content\Dto\ExternalContent|null
   *   The content.
   */
  public function get(string $id): ?ExternalContent {
    if (!$this->has($id)) {
      return NULL;
    }

    return $this->items[$id];
  }

  /**
   * Checks if content presented in collection.
   *
   * @param string $id
   *   The content ID.
   *
   * @return bool
   *   TRUE if content is presented.
   */
  public function has(string $id): bool {
    return \array_key_exists($id, $this->items);
  }

}
