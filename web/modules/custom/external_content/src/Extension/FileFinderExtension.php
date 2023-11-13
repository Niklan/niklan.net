<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;

/**
 * Provides a very basic extension with most useful settings.
 */
final class FileFinderExtension implements ConfigurableExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {

  }

  /**
   * {@inheritdoc}
   */
  public function configureSchema(ConfigurationBuilderInterface $builder): void {

  }

}
