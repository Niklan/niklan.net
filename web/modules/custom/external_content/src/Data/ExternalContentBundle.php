<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides an external content bundle.
 */
final class ExternalContentBundle {

  /**
   * The array with bundled items.
   *
   * Example:
   *
   * @code:
   * $items[$reason][$reason_id] = $document;
   * @endcode
   */
  protected array $items = [];

  public function add(string $reason, string $reason_id, ExternalContentDocument $document): self {
    $this->items[$reason][$reason_id] = $document;

    return $this;
  }

}
