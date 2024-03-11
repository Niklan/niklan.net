<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * {@selfdoc}
 */
final readonly class HtmlParserResult {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private bool $shouldContinue,
    private ?NodeInterface $node,
  ) {}

  /**
   * {@selfdoc}
   */
  public function shouldContinue(): bool {
    return $this->shouldContinue;
  }

  /**
   * {@selfdoc}
   */
  public function shouldNotContinue(): bool {
    return !$this->shouldContinue();
  }

  /**
   * {@selfdoc}
   */
  public function hasReplacement(): bool {
    return $this->node instanceof NodeInterface;
  }

  /**
   * {@selfdoc}
   */
  public function hasNoReplacement(): bool {
    return !$this->hasReplacement();
  }

  /**
   * {@selfdoc}
   */
  public function replacement(): ?NodeInterface {
    return $this->node;
  }

  /**
   * {@selfdoc}
   */
  public static function replaceAndContinue(NodeInterface $node): self {
    return new self(TRUE, $node);
  }

  /**
   * {@selfdoc}
   */
  public static function replaceAndStop(NodeInterface $node): self {
    return new self(FALSE, $node);
  }

  /**
   * {@selfdoc}
   */
  public static function pass(): self {
    return new self(TRUE, NULL);
  }

  /**
   * {@selfdoc}
   */
  public static function stop(): self {
    return new self(FALSE, NULL);
  }

}
