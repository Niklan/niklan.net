<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Blog;

use Drupal\app_contract\Contract\Node\Article;

interface ArticleRepository {

  public function findByExternalId(string $external_id): ?Article;

  /**
   * Finds articles whose external ID is not in the given list.
   *
   * @param list<string> $external_ids
   *   External IDs to keep.
   *
   * @return list<\Drupal\app_contract\Contract\Node\Article>
   *   Articles which are not present in the given list.
   */
  public function findAllExceptExternalIds(array $external_ids): array;

}
