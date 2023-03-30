<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents a code content.
 */
final class CodeContent implements MarkdownSourceInterface {

  /**
   * Constructs a new CodeContent instance.
   *
   * @param string $code
   *   The Markdown code.
   * @param \Drupal\content_export\Data\FrontMatter $frontMatter
   *   The additional settings as Front Matter.
   */
  public function __construct(
    protected string $code,
    protected FrontMatter $frontMatter,
  ) {}

  /**
   * Gets the code.
   *
   * @return string
   *   The Markdown code.
   */
  public function getCode(): string {
    return $this->code;
  }

  /**
   * Gets Front Matter.
   *
   * @return \Drupal\content_export\Data\FrontMatter
   *   The Front Matter.
   */
  public function getFrontMatter(): FrontMatter {
    return $this->frontMatter;
  }

}
