<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

/**
 * @template TRequest of \Drupal\external_content\Contract\Exporter\ContentExportRequest
 * @template TReturn
 */
interface ContentExporter {

  /**
   * @param TRequest $request
   * @return TReturn
   */
  public function export(ContentExportRequest $request): mixed;

}
