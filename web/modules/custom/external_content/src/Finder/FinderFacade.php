<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderFacadeInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Provides an external content finder.
 */
final class FinderFacade implements FinderFacadeInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * Constructs a new ExternalContentFinder instance.
   *
   * @param \Drupal\external_content\Contract\DependencyInjection\EnvironmentAwareClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected EnvironmentAwareClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function find(): ExternalContentFileCollection {
    $collection = new ExternalContentFileCollection();

    foreach ($this->environment->getFinders() as $finder) {
      $instance = $this
        ->classResolver
        ->getInstance($finder, FinderInterface::class, $this->getEnvironment());
      $finder_collection = $instance->find();
      $collection->merge($finder_collection);
    }

    return $collection;
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
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
