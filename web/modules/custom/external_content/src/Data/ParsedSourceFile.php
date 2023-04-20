<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents parsed source file.
 *
 * The source file should contain contents and meta information for the content.
 * When it extracted, these separate data stored in this value object.
 *
 * This class holds params (from Front Matter) and it's content, as well as
 * source file for references.
 */
final class ParsedSourceFile {

  /**
   * Constructs a new SourceDocument object.
   *
   * @param \Drupal\external_content\Data\SourceFile $file
   *   The parsed source file.
   * @param \Drupal\external_content\Data\SourceFileParams $params
   *   The source file content parameters (FrontMatter).
   * @param \Drupal\external_content\Data\SourceFileContent $content
   *   The source file content cleaned from FrontMatter.
   */
  public function __construct(
    protected SourceFile $file,
    protected SourceFileParams $params,
    protected SourceFileContent $content,
  ) {}

  /**
   * Gets source file.
   *
   * @return \Drupal\external_content\Data\SourceFile
   *   The source file.
   */
  public function getFile(): SourceFile {
    return $this->file;
  }

  /**
   * Gets parameters.
   *
   * @return \Drupal\external_content\Data\SourceFileParams
   *   The source file parameters.
   */
  public function getParams(): SourceFileParams {
    return $this->params;
  }

  /**
   * Gets content.
   *
   * @return \Drupal\external_content\Data\SourceFileContent
   *   The source file content.
   */
  public function getContent(): SourceFileContent {
    return $this->content;
  }

}
