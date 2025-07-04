<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser;

/**
 * @template T
 */
interface ParserSource {

  /**
   * @return T
   */
  public function getSourceData(): mixed;

}
