<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Tag;

interface TagUsageStatistics {

  /**
   * @return \stdClass[]
   *   An array with term results.
   */
  public function usage(?int $limit = NULL): array;

  public function count(int $tag_id): int;

  public function firstPublicationDate(int $tag_id): ?int;

  public function lastPublicationDate(int $tag_id): ?int;

}
