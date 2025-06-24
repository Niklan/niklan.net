<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\ContentImporterSource;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImporterSource<array>
 */
final class ArrayContentImporterSource implements ContentImporterSource {

  public function __construct(
    private array $array,
  ) {}

  public function getSourceData(): array {
    return $this->array;
  }

}
