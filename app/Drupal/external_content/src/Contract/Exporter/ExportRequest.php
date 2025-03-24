<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

use Drupal\external_content\DataStructure\Nodes\RootNode;

/**
 * @template TContext of \Drupal\external_content\Contract\Exporter\ExporterContext
 */
interface ExportRequest {

  public function getContent(): RootNode;

  /**
   * @return TContext
   */
  public function getContext(): ExporterContext;

}
