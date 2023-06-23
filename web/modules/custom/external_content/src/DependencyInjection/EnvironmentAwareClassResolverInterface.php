<?php declare(strict_types = 1);

namespace Drupal\external_content\DependencyInjection;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;

/**
 * Represents a class resolver for external content purposes.
 */
interface EnvironmentAwareClassResolverInterface {

  /**
   * Returns a class instance with a given class definition.
   *
   * @param string $definition
   *   A class or service name to instantiate.
   * @param string $expected_instance_of
   *   A FQN of expected class instance.
   * @param \Drupal\external_content\Contract\Environment\EnvironmentInterface $environment
   *   An environment for instantiated class.
   */
  public function getInstance(string $definition, string $expected_instance_of, EnvironmentInterface $environment): object;

}
