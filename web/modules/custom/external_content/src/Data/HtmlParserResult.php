<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Represents an HTML parser status.
 */
final class HtmlParserResult {

  public function __construct(
    protected bool $continue,
    protected ?NodeInterface $replacement = NULL,
  ) {}

  public static function replaceWith(NodeInterface $node): self {
    return new self(TRUE, $node);
  }

  public static function finalizeWith(NodeInterface $node): self {
    return new self(FALSE, $node);
  }

  public static function continue(): self {
    return new self(TRUE);
  }

  /**
   *
   */
  public static function finalize(): self {
    return new self(FALSE);
  }

  /**
   *
   */
  public function hasReplacement(): bool {
    return isset($this->replacement);
  }

  /**
   *
   */
  public function getReplacement(): ?NodeInterface {
    return $this->replacement;
  }

  /**
   *
   */
  public function shouldContinue(): bool {
    return $this->continue;
  }

}
