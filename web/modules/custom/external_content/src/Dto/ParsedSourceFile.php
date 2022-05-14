<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents parsed source file.
 *
 * The source file should contain contents and meta information for the content.
 * When it extracted, these separate data stored in this value object.
 *
 * This class holds metadata (from Front Matter) and it's content, as well as
 * source file for references.
 */
final class ParsedSourceFile {

  /**
   * Constructs a new SourceDocument object.
   *
   * @param \Drupal\external_content\Dto\SourceFile $file
   *   The parsed source file.
   * @param \Drupal\external_content\Dto\SourceFileMetadata $metadata
   *   The source file content metadata (FrontMatter).
   * @param \Drupal\external_content\Dto\SourceFileContent $content
   *   The source file content cleaned from FrontMatter.
   */
  public function __construct(
    protected SourceFile $file,
    protected SourceFileMetadata $metadata,
    protected SourceFileContent $content,
  ) {}

  /**
   * Gets source file.
   *
   * @return \Drupal\external_content\Dto\SourceFile
   *   The source file.
   */
  public function getFile(): SourceFile {
    return $this->file;
  }

  /**
   * Gets metadata.
   *
   * @return \Drupal\external_content\Dto\SourceFileMetadata
   *   The source file metadata.
   */
  public function getMetadata(): SourceFileMetadata {
    return $this->metadata;
  }

  /**
   * Gets content.
   *
   * @return \Drupal\external_content\Dto\SourceFileContent
   *   The source file content.
   */
  public function getContent(): SourceFileContent {
    return $this->content;
  }

}
