<?php

declare(strict_types=1);

namespace Drupal\niklan\Contract\Utility;

interface TagStatisticsInterface {

  /**
   * @return \stdClass[]
   *   An array with term results.
   */
  public function getBlogEntryUsage(?int $limit = NULL): array;

}
