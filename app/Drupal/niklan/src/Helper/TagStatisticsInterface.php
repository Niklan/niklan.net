<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

/**
 * Defines an interface for tag statistic helper.
 */
interface TagStatisticsInterface {

  /**
   * Gets most used tags from blog entries.
   *
   * @param int|null $limit
   *   The maximum amount of entries to fetch.
   *
   * @return \stdClass[]
   *   An array with term results.
   */
  public function getBlogEntryUsage(?int $limit = NULL): array;

}
