<?php

declare(strict_types=1);

namespace Drupal\niklan\Data;

final class SearchParams {

  public function __construct(
    protected ?string $keys,
    protected int $limit,
    protected int $offset = 0,
  ) {}

  public function getKeys(): ?string {
    return $this->keys;
  }

  public function getLimit(): int {
    return $this->limit;
  }

  public function getOffset(): int {
    return $this->offset;
  }

}
