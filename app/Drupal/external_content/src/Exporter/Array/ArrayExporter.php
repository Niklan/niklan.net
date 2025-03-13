<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\Contract\Renderer\Exporter;

/**
 * @implements \Drupal\external_content\Contract\Renderer\Exporter<\Drupal\external_content\Exporter\Array\ArrayExportRequest, \Drupal\external_content\Exporter\Array\ArrayElement>
 */
final class ArrayExporter implements Exporter {

  /**
   * @param \Drupal\external_content\Exporter\Array\ArrayExportRequest $request
   */
  public function export(ExportRequest $request): ArrayElement {
    // @todo
    $array = new ArrayElement('root');

    return $request->getArrayStructureBuilder()->build();
  }

}
