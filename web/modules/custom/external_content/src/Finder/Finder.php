<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\SourceCollection;

/**
 * Provides a main finder to rule them all.
 */
final class Finder implements FinderInterface, EnvironmentAwareInterface {

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
      $collection->merge($finder->find());
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
