<?php

namespace Drupal\niklan_external_content\Dto;

/**
 * Represents a source file with a content.
 */
final class SourceFile {

  /**
   * The source content path URI.
   */
  protected string $realpath;

  /**
   * The source file information.
   */
  protected ?\SplFileInfo $file = NULL;

  /**
   * Constructs a new SourceContent object.
   *
   * @param string $realpath
   *   The content URI path.
   */
  public function __construct(string $realpath) {
    $this->realpath = $realpath;
  }

  /**
   * Checks is files is readable.
   *
   * @return bool
   *   TRUE is readable, FALSE if file is not readable or not exists.
   */
  public function isReadable(): bool {
    return $this->getFile()->isReadable();
  }

  /**
   * Gets file information.
   *
   * @return \SplFileInfo
   *   The file information object.
   */
  protected function getFile(): \SplFileInfo {
    if (!$this->file) {
      $this->file = new \SplFileInfo($this->realpath);
    }

    return $this->file;
  }

  /**
   * Gets file contents.
   *
   * @return string
   *   The file contents.
   */
  public function getContents(): string {
    return \file_get_contents($this->getRealpath());
  }

  /**
   * Gets absolute path to the file.
   *
   * @return string
   *   The URI path to content source.
   */
  public function getRealpath(): string {
    return $this->realpath;
  }

  /**
   * {@inheritdoc}
   */
  public function __sleep() {
    $vars = \get_object_vars($this);
    // SplFileInfo is not serializable and don't need to be serialized.
    unset($vars['file']);
    return \array_keys($vars);
  }

}
