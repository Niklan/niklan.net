<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Exporter\Exporter;
use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\DataStructure\RenderArray;

/**
 * @implements \Drupal\external_content\Contract\Exporter\Exporter<\Drupal\external_content\Exporter\RenderArray\RenderArrayExportRequest, \Drupal\external_content\DataStructure\RenderArray>
 */
final class RenderArrayExporter implements Exporter {

  /**
   * @param \Drupal\external_content\Exporter\RenderArray\RenderArrayExportRequest $request
   */
  public function export(ExportRequest $request): RenderArray {
    $root = new RenderArray();
    $builder_request = new RenderArrayBuildRequest($request->getContent(), $root, $request);
    $request->getRenderArrayBuilder()->buildChildren($builder_request);
    return $root;
  }

}
