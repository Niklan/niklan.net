<?php

declare(strict_types=1);

namespace Drupal\niklan\Blog\Contract;

use Drupal\niklan\Node\Entity\BlogEntry;

interface BlogRepository {

  public function findByExternalId(string $external_id): ?BlogEntry;

}
