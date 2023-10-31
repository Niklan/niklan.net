<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Configuration;

/**
 * Defines an interface for configuration aware environment component.
 */
interface ConfigurationAwareInterface {

  /**
   * {@selfdoc}
   */
  public function setConfiguration(Configuration $configuration): void;

}
