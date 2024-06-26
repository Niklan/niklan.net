<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Extension;

use League\Config\ConfigurationBuilderInterface;

/**
 * Provides an extension that supports configuration.
 */
interface ConfigurableExtensionInterface extends ExtensionInterface {

  /**
   * {@selfdoc}
   */
  public function configureSchema(ConfigurationBuilderInterface $builder): void;

}
