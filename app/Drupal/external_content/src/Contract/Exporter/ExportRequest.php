<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

use Drupal\external_content\Nodes\Document;

/**
 * @template TContext of \Drupal\external_content\Contract\Exporter\ExporterContext
 */
interface ExportRequest {

  public function getContent(): Document;

  /**
   * @return TContext
   */
  public function getContext(): ExporterContext;

}
