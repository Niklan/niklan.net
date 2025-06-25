<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

/**
 * @template T
 */
interface ImporterSource {

  /**
   * @return T
   */
  public function getSourceData(): mixed;

}
