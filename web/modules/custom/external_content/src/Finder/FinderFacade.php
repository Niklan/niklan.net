<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

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
   * {@inheritdoc}
   */
  public function find(): ExternalContentFileCollection {
    $collection = new ExternalContentFileCollection();

    foreach ($this->environment->getFinders() as $finder) {
      \assert($finder instanceof FinderInterface);
      $finder_collection = $finder->find();
      $collection->merge($finder_collection);
    }

    return $collection;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}