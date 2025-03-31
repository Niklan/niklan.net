<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\ImporterSource;

/**
 * @implements \Drupal\external_content\Contract\Importer\ImporterSource<array>
 */
final class ArrayImporterSource implements ImporterSource {

  public function __construct(
    private array $array,
  ) {}

  public function getSourceData(): array {
    return $this->array;
  }

}
