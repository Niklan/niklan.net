<?php

declare(strict_types=1);

namespace Drupal\niklan\Tag\Contract;

use Drupal\niklan\Tag\Entity\Tag;

interface TagRepository {

  public function findByExternalId(string $external_id): ?Tag;

}
