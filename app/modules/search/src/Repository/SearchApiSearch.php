<?php

declare(strict_types=1);

namespace Drupal\app_search\Repository;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\Utility\QueryHelperInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class SearchApiSearch implements CacheableDependencyInterface {

  abstract protected function getIndexId(): string;

  public function __construct(
    #[Autowire(service: 'search_api.query_helper')]
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
    \assert($index instanceof IndexInterface);

    return $this->queryHelper->createQuery($index);
  }

}
