<?php

declare(strict_types=1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class FinderManager implements FinderManagerInterface {

  public function __construct(
    private ContainerInterface $container,
    private array $finders = [],
  ) {}

  #[\Override]
  public function find(EnvironmentInterface $environment): SourceCollection {
    $collection = new SourceCollection();

    foreach ($environment->getFinders() as $finder) {
      \assert($finder instanceof FinderInterface);
      $finder_result = $finder->find();

      if ($finder_result->hasNoResults()) {
        continue;
      }

      $result_collection = $finder_result->results();
      // @todo Remove when resolved: https://github.com/phpstan/phpstan/issues/12495
      \assert($result_collection instanceof SourceCollection);
      $collection->merge($result_collection);
    }

    return $collection;
  }

  #[\Override]
  public function get(string $finder_id): FinderInterface {
    if (!$this->has($finder_id)) {
      throw new MissingContainerDefinitionException(
        type: 'finder',
        id: $finder_id,
      );
    }

    $service = $this->finders[$finder_id]['service'];

    return $this->container->get($service);
  }

  #[\Override]
  public function has(string $finder_id): bool {
    return \array_key_exists($finder_id, $this->finders);
  }

  #[\Override]
  public function list(): array {
    return $this->finders;
  }

}
