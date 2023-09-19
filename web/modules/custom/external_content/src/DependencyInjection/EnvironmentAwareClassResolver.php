<?php declare(strict_types = 1);

namespace Drupal\external_content\DependencyInjection;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\external_content\Contract\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;

/**
 * Provides a environment aware class resolver.
 */
final class EnvironmentAwareClassResolver implements EnvironmentAwareClassResolverInterface {

  /**
   * The array with already instantiated classes.
   */
  protected array $instances = [];

  /**
   * Constructs a new EnvironmentAwareClassResolver instance.
   *
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The Drupal class resolver.
   */
  public function __construct(
    protected ClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getInstance(string $definition, string $expected_instance_of, EnvironmentInterface $environment): object {
    if (\array_key_exists($definition, $this->instances)) {
      return $this->instances[$definition];
    }

    $instance = $this->classResolver->getInstanceFromDefinition($definition);

    if (!$instance instanceof $expected_instance_of) {
      $message = \sprintf(
        'The "%s" definition is expected to be "%s".',
        $definition,
        $expected_instance_of,
      );

      throw new \InvalidArgumentException($message);
    }

    if (\is_subclass_of($instance, EnvironmentAwareInterface::class)) {
      $instance->setEnvironment($environment);
    }

    $this->instances[$definition] = $instance;

    return $instance;
  }

}
