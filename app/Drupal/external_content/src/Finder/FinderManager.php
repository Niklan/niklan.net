<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Data\SourceCollection;

/**
 * Provides a main finder to rule them all.
 */
final class FinderManager implements FinderManagerInterface {

  /**
   * {@selfdoc}
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function find(): SourceCollection {
    $collection = new SourceCollection();

    foreach ($this->environment->getFinders() as $finder) {
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
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
