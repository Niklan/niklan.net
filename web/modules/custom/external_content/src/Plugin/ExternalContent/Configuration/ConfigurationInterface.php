<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Configuration;

/**
 * Provides an interface for external content settings.
 */
interface ConfigurationInterface {

  /**
   * Gets a plugin ID.
   *
   * @return string
   *   The plugin ID.
   */
  public function id(): string;

}
