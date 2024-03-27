<?php declare(strict_types = 1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\IdentifiedSourceBundleCollection;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an external content bundler.
 */
final readonly class BundlerManager implements BundlerManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private array $bundlers = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  public function bundle(IdentifiedSourceCollection $source_collection, EnvironmentInterface $environment): IdentifiedSourceBundleCollection {
    $bundles = [];

    foreach ($source_collection->sources() as $source) {
      \assert($source instanceof IdentifiedSource);
      $this->bundleSource($source, $bundles, $environment);
    }

    return $this->packBundles($bundles);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get(string $bundler_id): BundlerInterface {
    if (!$this->has($bundler_id)) {
      throw new MissingContainerDefinitionException(
        type: 'bundler',
        id: $bundler_id,
      );
    }

    $service = $this->bundlers[$bundler_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function has(string $bundler_id): bool {
    return \array_key_exists($bundler_id, $this->bundlers);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function list(): array {
    return $this->bundlers;
  }

  /**
   * {@selfdoc}
   */
  private function bundleSource(IdentifiedSource $source, array &$bundles, EnvironmentInterface $environment): void {
    foreach ($environment->getBundlers() as $bundler) {
      \assert($bundler instanceof BundlerInterface);
      $result = $bundler->bundle($source);

      if ($result->shouldNotBeBundled()) {
        continue;
      }

      $bundles[$result->bundleId][] = $source;
    }
  }

  /**
   * {@selfdoc}
   */
  protected function packBundles(array $identified_bundles): IdentifiedSourceBundleCollection {
    $bundle_collection = new IdentifiedSourceBundleCollection();

    foreach ($identified_bundles as $id => $sources) {
      $bundle = new IdentifiedSourceBundle($id);

      foreach ($sources as $source) {
        $bundle->add($source);
      }

      $bundle_collection->add($bundle);
    }

    return $bundle_collection;
  }

}
