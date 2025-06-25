<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

/**
 * @template TRequest of \Drupal\external_content\Contract\Exporter\ExportRequest
 * @template TReturn
 */
interface Exporter {

  /**
   * @param TRequest $request
   * @return TReturn
   */
  public function export(ExportRequest $request): mixed;

}
