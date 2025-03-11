<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ImporterSource;

/**
 * @implements \Drupal\external_content\Contract\Importer\ImporterSource<string>
 */
final class HtmlImporterSource implements ImporterSource {

  public function __construct(
    private string $rawHtml,
  ) {}

  /**
   * @return string
   */
  public function getSourceData(): mixed {
    return $this->rawHtml;
  }

}
