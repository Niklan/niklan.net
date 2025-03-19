<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\Contract\Exporter\Exporter;
use Drupal\external_content\Exporter\Array\Builder\ArrayBuildRequest;
use Drupal\external_content\Exporter\Array\Builder\ArrayElement;

/**
 * @implements \Drupal\external_content\Contract\Exporter\Exporter<\Drupal\external_content\Exporter\Array\ArrayExportRequest, \Drupal\external_content\Exporter\Array\Builder\ArrayElement>
 */
final class ArrayExporter implements Exporter {

  /**
   * @param \Drupal\external_content\Exporter\Array\ArrayExportRequest $request
   */
  public function export(ExportRequest $request): ArrayElement {
    $root = new ArrayElement('root');
    $builder_request = new ArrayBuildRequest($request->getContent(), $root, $request);
    $request->getArrayStructureBuilder()->buildChildren($builder_request);

    return $root;
  }

}
