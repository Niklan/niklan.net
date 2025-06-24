<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

/**
 * @template TSource of \Drupal\external_content\Contract\Importer\ContentImporterSource
 * @template TContext of \Drupal\external_content\Contract\Importer\ContentImporterContext
 */
interface ContentImportRequest {

  /**
   * @return TSource
   */
  public function getSource(): ContentImporterSource;

  /**
   * @return TContext
   */
  public function getContext(): ContentImporterContext;

}
