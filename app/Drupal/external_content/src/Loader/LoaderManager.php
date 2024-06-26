<?php declare(strict_types = 1);

namespace Drupal\external_content\Loader;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderManagerInterface;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResultCollection;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final class LoaderManager implements LoaderManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private array $loaders = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function load(IdentifiedSourceBundle $bundle, EnvironmentInterface $environment): LoaderResultCollection {
    $result_collection = new LoaderResultCollection();

    foreach ($environment->getLoaders() as $loader) {
      \assert($loader instanceof LoaderInterface);
      $result = $loader->load($bundle);
      $result_collection->addResult($result);

      if ($result->shouldNotContinue()) {
        break;
      }
    }

    return $result_collection;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get(string $loader_id): LoaderInterface {
    if (!$this->has($loader_id)) {
      throw new MissingContainerDefinitionException(
        type: 'loader',
        id: $loader_id,
      );
    }

    $service = $this->loaders[$loader_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function has(string $loader_id): bool {
    return \array_key_exists($loader_id, $this->loaders);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function list(): array {
    return $this->loaders;
  }

}
