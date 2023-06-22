<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a single external content with HTML.
 */
final class ExternalContentHtml {

  /**
   * The additional data.
   */
  protected Data $data;

  /**
   * Constructs a new ExternalContentHtml instance.
   *
   * @param \Drupal\external_content\Data\ExternalContentFile $file
   *   The external content file.
   * @param string $content
   *   The content.
   */
  public function __construct(
    protected ExternalContentFile $file,
    protected string $content,
  ) {}

  /**
   * Gets the external content file.
   *
   * @return \Drupal\external_content\Data\ExternalContentFile
   *   The external content fle.
   */
  public function getFile(): ExternalContentFile {
    return $this->file;
  }

  /**
   * Sets the content.
   *
   * @param string $content
   *   The content.
   *
   * @return $this
   */
  public function setContent(string $content): self {
    $this->content = $content;

    return $this;
  }

  /**
   * Gets the content.
   *
   * @return string
   *   The content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
