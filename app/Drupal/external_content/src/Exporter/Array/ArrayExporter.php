<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\Contract\Renderer\Exporter;

/**
 * @implements \Drupal\external_content\Contract\Renderer\Exporter<\Drupal\external_content\Exporter\Array\ArrayExportRequest, array>
 */
final class ArrayExporter implements Exporter {

  public function export(ExportRequest $request): array {
    // TODO: Implement export() method.
  }

}
