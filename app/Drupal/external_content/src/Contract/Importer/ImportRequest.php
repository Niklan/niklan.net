<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

/**
 * @template TSource of \Drupal\external_content\Contract\Importer\ImporterSource
 * @template TContext of \Drupal\external_content\Contract\Importer\ImporterContext
 */
interface ImportRequest {

  /**
   * @return TSource
   */
  public function getSource(): ImporterSource;

  /**
   * @return TContext
   */
  public function getContext(): ImporterContext;

}
