<?php

declare(strict_types=1);

namespace Drupal\external_content\Utils;

/**
 * @template T
 */
final class Registry {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<T>
   */
  private PrioritizedList $list;

  public function __construct() {
    $this->list = new PrioritizedList();
  }

  /**
   * @param T $item
   * @param int $priority
   */
  public function add(mixed $item, int $priority = 0): void {
    $this->list->add($item, $priority);
  }

  /**
   * @return \Traversable<T>
   */
  public function getAll(): \Traversable {
    return $this->list;
  }

}
