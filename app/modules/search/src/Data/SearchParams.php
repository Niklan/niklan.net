<?php

declare(strict_types=1);

namespace Drupal\app_search\Data;

final readonly class SearchParams {

  public function __construct(
    public ?string $keys,
    public int $limit,
    public int $offset = 0,
  ) {}

}
