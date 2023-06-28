<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Data\Attributes;

/**
 * Represents a bundler result with identified document.
 */
interface BundlerResultIdentifiedInterface {

  /**
   * Gets the identifier.
   */
  public function id(): string;

  /**
   * Gets the identified document attributes.
   */
  public function attributes(): Attributes;

}
