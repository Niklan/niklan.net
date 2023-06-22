<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Represents an HTML parser status.
 */
final class HtmlParserResult {

  /**
   * Constructs a new HtmlParserResult instance.
   *
   * @param bool $continue
   *   Indicates that result allow to continue parsing.
   * @param \Drupal\external_content\Contract\Node\NodeInterface|null $replacement
   *   The result element which replaces parsed element.
   */
  public function __construct(
    protected bool $continue,
    protected ?NodeInterface $replacement = NULL,
  ) {}

  /**
   * Replaces parsed element and continue parsing children.
   *
   * @param \Drupal\external_content\Contract\Node\NodeInterface $node
   *   The replacement.
   */
  public static function replaceWith(NodeInterface $node): self {
    return new self(TRUE, $node);
  }

  /**
   * Replaces parsed element and stop parsing its children.
   *
   * @param \Drupal\external_content\Contract\Node\NodeInterface $node
   *   The replacement.
   */
  public static function finalizeWith(NodeInterface $node): self {
    return new self(FALSE, $node);
  }

  /**
   * Indicates that parsing of element should continue.
   */
  public static function continue(): self {
    return new self(TRUE);
  }

  /**
   * Indicates that parsing of element should be stopped without replacement.
   */
  public static function finalize(): self {
    return new self(FALSE);
  }

  /**
   * Checks for replacement element.
   *
   * @return bool
   *   TRUE if there is a replacement, FALSE otherwise.
   */
  public function hasReplacement(): bool {
    return isset($this->replacement);
  }

  /**
   * Gets the replacement.
   *
   * @return \Drupal\external_content\Contract\Node\NodeInterface|null
   *   The replacement.
   */
  public function getReplacement(): ?NodeInterface {
    return $this->replacement;
  }

  /**
   * Checks should parse continue or not.
   *
   * @return bool
   *   TRUE if should continue, FALSE otherwise.
   */
  public function shouldContinue(): bool {
    return $this->continue;
  }

}
