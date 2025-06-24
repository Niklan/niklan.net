<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ContentImporterSource;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImporterSource<string>
 */
final readonly class HtmlContentImporterSource implements ContentImporterSource {

  public function __construct(
    private string $rawHtml,
  ) {}

  public function getSourceData(): string {
    return $this->rawHtml;
  }

}
