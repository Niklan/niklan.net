<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Repository;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\Utility\QueryHelperInterface;

abstract class SearchApiSearch implements CacheableDependencyInterface {

  abstract protected function getIndexId(): string;

  public function __construct(
    protected QueryHelperInterface $queryHelper,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function getCacheContexts(): array {
    return [];
  }

  #[\Override]
  public function getCacheTags(): array {
    return [
      'search_api_list:' . $this->getIndexId(),
    ];
  }

  #[\Override]
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

  protected function getQuery(): QueryInterface {
    $index_storage = $this->entityTypeManager->getStorage('search_api_index');
    $index = $index_storage->load($this->getIndexId());

    return $this->queryHelper->createQuery($index);
  }

}
