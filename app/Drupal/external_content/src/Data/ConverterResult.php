<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Source\Html;

final readonly class ConverterResult {

  public function __construct(
    private ?Html $result,
  ) {}

  public static function pass(): self {
    return new self(NULL);
  }

  public static function withHtml(Html $html): self {
    return new self($html);
  }

  /**
   * @phpstan-assert-if-true !null $this->getResult()
   */
  public function hasResult(): bool {
    return !$this->hasNoResult();
  }

  /**
   * @phpstan-assert-if-true null $this->getResult()
   */
  public function hasNoResult(): bool {
    return \is_null($this->result);
  }

  public function getResult(): ?Html {
    return $this->result;
  }

}
