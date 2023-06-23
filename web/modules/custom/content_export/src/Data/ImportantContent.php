<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents important content.
 */
final class ImportantContent implements MarkdownSourceInterface {

  /**
   * Constructs a new ImportantContent instance.
   *
   * @param string $type
   *   The important element type.
   * @param \Drupal\content_export\Data\Content $content
   *   The inner content.
   */
  public function __construct(
    protected string $type,
    protected Content $content,
  ) {}

  /**
   * Gets type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Gets inner content.
   */
  public function getContent(): Content {
    return $this->content;
  }

}
