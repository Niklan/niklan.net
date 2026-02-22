<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Tag;

interface TagRepository {

  public function findByExternalId(string $external_id): ?Tag;

}
