<?php

declare(strict_types=1);

namespace Drupal\external_content\Source;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Data;

final readonly class Html implements SourceInterface {

  public function __construct(
    private string $contents,
    private ?Data $data = new Data(),
  ) {}

  #[\Override]
  public function data(): Data {
    return $this->data;
  }

  #[\Override]
  public function type(): string {
    return 'text/html';
  }

  #[\Override]
  public function contents(): string {
    return $this->contents;
  }

}
