<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Source;

use Drupal\external_content\Data\Data;

/**
 * Represents a single source item of external content.
 */
interface SourceInterface {

  /**
   * Returns a unique identifier for the source.
   */
  public function id(): string;

  /**
   * Returns additional information related to source.
   */
  public function data(): Data;

  /**
   * Returns a source type.
   */
  public function type(): string;

  /**
   * Returns a source contents.
   */
  public function contents(): string;

}
