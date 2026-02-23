<?php

declare(strict_types=1);

namespace Drupal\app_search\Data;

final class EntitySearchResult {

  public function __construct(
    public string $entityTypeId,
    public int|string $entityId,
    public string $language,
  ) {}

}
