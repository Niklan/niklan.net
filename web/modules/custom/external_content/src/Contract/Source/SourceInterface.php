<?php declare(strict_types=1);

namespace Drupal\external_content\Contract\Source;

/**
 * Represents a single source item of external content.
 */
interface SourceInterface {

  /**
   * Returns a unique identifier for the source.
   */
  public function id(): string;

}
