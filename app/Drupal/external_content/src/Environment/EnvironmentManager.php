<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Exception\MissingEnvironmentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final class EnvironmentManager implements EnvironmentManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private readonly ContainerInterface $container,
    private readonly array $environments = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getEnvironment(string $environment_id): EnvironmentInterface {
    if (!\array_key_exists($environment_id, $this->environments)) {
      throw new MissingEnvironmentException($environment_id);
    }

    $service = $this->environments[$environment_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getEnvironments(): array {
    return $this->environments;
  }

}
