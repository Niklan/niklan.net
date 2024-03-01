<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Source\Html;

/**
 * {@selfdoc}
 */
final readonly class ConverterResult {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ?Html $result,
  ) {}

  /**
   * {@selfdoc}
   */
  public static function pass(): self {
    return new self(NULL);
  }

  /**
   * {@selfdoc}
   */
  public static function withHtml(Html $html): self {
    return new self($html);
  }

  /**
   * {@selfdoc}
   */
  public function hasNoResult(): bool {
    return \is_null($this->result);
  }

  /**
   * {@selfdoc}
   */
  public function hasResult(): bool {
    return !$this->hasNoResult();
  }

  /**
   * {@selfdoc}
   */
  public function getResult(): ?Html {
    return $this->result;
  }

}
