<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final class IdentifierResult {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ?IdentifiedSource $result = NULL,
  ) {}

  /**
   * {@selfdoc}
   */
  public function isNotIdentified(): bool {
    return \is_null($this->result);
  }

  /**
   * {@selfdoc}
   */
  public function isIdentified(): bool {
    return !$this->isNotIdentified();
  }

  /**
   * {@selfdoc}
   */
  public function result(): ?IdentifiedSource {
    return $this->result;
  }

  /**
   * {@selfdoc}
   */
  public static function identified(IdentifiedSource $identified_source): self {
    return new self($identified_source);
  }

  /**
   * {@selfdoc}
   */
  public static function notIdentified(): self {
    return new self();
  }

}
