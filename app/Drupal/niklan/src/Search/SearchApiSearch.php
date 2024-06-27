<?php

declare(strict_types=1);

namespace Drupal\niklan\Search;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\Utility\QueryHelperInterface;

/**
 * Provides a base for Search API based searches.
 */
abstract class SearchApiSearch implements CacheableDependencyInterface {

  /**
   * Constructs a new SearchApiSearch instance.
   *
   * @param \Drupal\search_api\Utility\QueryHelperInterface $queryHelper
   *   The Search API query helper.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected QueryHelperInterface $queryHelper,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return [
      'search_api_list:' . $this->getIndexId(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

  /**
   * Gets a Search API index.
   */
  abstract protected function getIndexId(): string;

  /**
   * Builds base query.
   */
  protected function getQuery(): QueryInterface {
    $index_storage = $this->entityTypeManager->getStorage('search_api_index');
    $index = $index_storage->load($this->getIndexId());

    return $this->queryHelper->createQuery($index);
  }

}
