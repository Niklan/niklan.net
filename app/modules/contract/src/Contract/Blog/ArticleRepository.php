<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Blog;

use Drupal\app_contract\Contract\Node\Article;

interface ArticleRepository {

  public function findByExternalId(string $external_id): ?Article;

}
