<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContentFinderInterface;
use Drupal\external_content\Contract\FinderInterface;
use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Provides an external content finder.
 */
final class ExternalContentFinder implements ExternalContentFinderInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * The array with finder instances.
   */
  protected array $finderInstances = [];

  /**
   * Constructs a new ExternalContentFinder instance.
   *
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected ClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function find(): ExternalContentFileCollection {
    $collection = new ExternalContentFileCollection();

    foreach ($this->environment->getFinders() as $finder) {
      $instance = $this->getFinderInstance($finder);
      $finder_collection = $instance->find($this->environment);
      $collection->merge($finder_collection);
    }

    return $collection;
  }

  /**
   * Gets the finder instance.
   *
   * @param string $finder
   *   The finder class.
   *
   * @return \Drupal\external_content\Contract\FinderInterface
   *   The finder instance.
   */
  protected function getFinderInstance(string $finder): FinderInterface {
    if (\array_key_exists($finder, $this->finderInstances)) {
      return $this->finderInstances[$finder];
    }

    $instance = $this
      ->classResolver
      ->getInstanceFromDefinition($finder);
    \assert($instance instanceof FinderInterface);
    $this->finderInstances[$finder] = $instance;

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): self {
    $this->environment = $environment;

    return $this;
  }

}
