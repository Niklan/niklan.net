<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final readonly class FinderManager implements FinderManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private array $finders = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  public function find(EnvironmentInterface $environment): SourceCollection {
    $collection = new SourceCollection();

    foreach ($environment->getFinders() as $finder) {
      \assert($finder instanceof FinderInterface);
      $finder_result = $finder->find();

      if ($finder_result->hasNoResults()) {
        continue;
      }

      $collection->merge($finder_result->results());
    }

    return $collection;
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function has(string $finder_id): bool {
    return \array_key_exists($finder_id, $this->finders);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function list(): array {
    return $this->finders;
  }

}
