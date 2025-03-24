<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\Importer;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\DataStructure\Nodes\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Importer\Importer<\Drupal\external_content\Importer\Array\ArrayImportRequest>
 */
final readonly class ArrayImporter implements Importer {

  /**
   * @param \Drupal\external_content\Importer\Array\ArrayImportRequest $request
   */
  public function import(ImportRequest $request): RootNode {

  }

}
