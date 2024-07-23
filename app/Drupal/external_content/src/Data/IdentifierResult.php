<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final class IdentifierResult {

  public function __construct(
    private ?IdentifiedSource $result = NULL,
  ) {}

  public static function identified(IdentifiedSource $identified_source): self {
    return new self($identified_source);
  }

  public static function notIdentified(): self {
    return new self();
  }

  public function isIdentified(): bool {
    return !$this->isNotIdentified();
  }

  public function isNotIdentified(): bool {
    return \is_null($this->result);
  }

  public function result(): ?IdentifiedSource {
    return $this->result;
  }

}
