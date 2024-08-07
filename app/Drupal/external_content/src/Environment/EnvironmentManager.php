<?php

declare(strict_types=1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class EnvironmentManager implements EnvironmentManagerInterface {

  public function __construct(
    private ContainerInterface $container,
    private array $environments = [],
  ) {}

  #[\Override]
  public function get(string $environment_id): EnvironmentInterface {
    if (!$this->has($environment_id)) {
      throw new MissingContainerDefinitionException(
        type: 'environment',
        id: $environment_id,
      );
    }

    $service = $this->environments[$environment_id]['service'];

    return $this->container->get($service);
  }

  #[\Override]
  public function has(string $environment_id): bool {
    return \array_key_exists($environment_id, $this->environments);
  }

  #[\Override]
  public function list(): array {
    return $this->environments;
  }

}
