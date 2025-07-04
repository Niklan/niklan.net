<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Array;

use Drupal\external_content\Contract\Parser\ParserSource;

/**
 * @implements \Drupal\external_content\Contract\Parser\ParserSource<array>
 */
final class ArrayParserSource implements ParserSource {

  public function __construct(
    private array $array,
  ) {}

  public function getSourceData(): array {
    return $this->array;
  }

}
