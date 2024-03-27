<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents a heading content.
 */
final class HeadingContent implements MarkdownSourceInterface {

  /**
   * Constructs a new HeadingContent instance.
   *
   * @param int $level
   *   The heading level.
   * @param string $heading
   *   The heading value.
   */
  public function __construct(
    protected int $level,
    protected string $heading,
  ) {}

  /**
   * Gets heading level.
   */
  public function getLevel(): int {
    return $this->level;
  }

  /**
   * Gets heading.
   */
  public function getHeading(): string {
    return $this->heading;
  }

}
