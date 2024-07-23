<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

interface TagStatisticsInterface {

  /**
   * @return \stdClass[]
   *   An array with term results.
   */
  public function getBlogEntryUsage(?int $limit = NULL): array;

}
