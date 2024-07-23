<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

final readonly class HtmlParserResult {

  public function __construct(
    private bool $shouldContinue,
    private ?NodeInterface $node,
  ) {}

  public static function replace(NodeInterface $node): self {
    return new self(FALSE, $node);
  }

  public static function pass(): self {
    return new self(TRUE, NULL);
  }

  public static function stop(): self {
    return new self(FALSE, NULL);
  }

  public function shouldContinue(): bool {
    return $this->shouldContinue;
  }

  public function shouldNotContinue(): bool {
    return !$this->shouldContinue();
  }

  public function hasReplacement(): bool {
    return $this->node instanceof NodeInterface;
  }

  public function hasNoReplacement(): bool {
    return !$this->hasReplacement();
  }

  public function replacement(): ?NodeInterface {
    return $this->node;
  }

}
