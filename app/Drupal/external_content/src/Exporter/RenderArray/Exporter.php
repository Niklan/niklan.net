<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Exporter\ContentExporter;
use Drupal\external_content\Contract\Exporter\ContentExportRequest;
use Drupal\external_content\DataStructure\ArrayElement;

/**
 * @implements \Drupal\external_content\Contract\Exporter\ContentExporter<\Drupal\external_content\Exporter\RenderArray\ExportRequest, \Drupal\external_content\DataStructure\ArrayElement>
 */
final class Exporter implements ContentExporter {

  /**
   * @param \Drupal\external_content\Exporter\Array\ExportRequest $request
   */
  public function export(ContentExportRequest $request): ArrayElement {
    $root = new ArrayElement('root');
    $builder_request = new BuildRequest($request->getContent(), $root, $request);
    $request->getArrayStructureBuilder()->buildChildren($builder_request);

    return $root;
  }

}
