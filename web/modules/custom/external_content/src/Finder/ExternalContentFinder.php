<?php declare(strict_types=1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContentFinderInterface;
use Drupal\external_content\Data\ExternalContentFileCollection;

final class ExternalContentFinder implements ExternalContentFinderInterface {

  public function __construct(
    protected EnvironmentInterface $environment,
  ) {}

  public function find(): ExternalContentFileCollection {
    $collection = new ExternalContentFileCollection();

    foreach ($this->environment->getFinders() as $finder) {
      $finder_collection = $finder->find($this->environment);
      $collection->merge($finder_collection);
    }

    return $collection;
  }

}
