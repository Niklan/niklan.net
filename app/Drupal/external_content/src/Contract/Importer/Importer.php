<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

use Drupal\external_content\Nodes\Document;

/**
 * @template T of \Drupal\external_content\Contract\Importer\ImportRequest
 */
interface Importer {

  /**
   * @param T $request
   */
  public function import(ImportRequest $request): Document;

}
