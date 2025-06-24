<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Importer\ContentImporterSource;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImporterSource<string>
 */
final readonly class MarkdownSourceContent implements ContentImporterSource {

  public function __construct(
    private string $markdown,
  ) {}

  public function getSourceData(): string {
    return $this->markdown;
  }

}
