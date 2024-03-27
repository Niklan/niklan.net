<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Extension;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

/**
 * {@selfdoc}
 */
final class ConfigurableExtension implements ConfigurableExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $builder->addSchema('foo', Expect::string());
    $builder->addSchema('bar', Expect::string());
  }

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    // We don't need it for testing.
  }

}
