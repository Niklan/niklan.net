<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ExporterContext;
use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\Node\RootNode;

final class ArrayExportRequest implements ExportRequest {

  public function getContent(): RootNode {
    // TODO: Implement getContent() method.
  }

  public function getContext(): ExporterContext {
    // TODO: Implement getContext() method.
  }

}
