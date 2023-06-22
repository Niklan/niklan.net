<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a source file with a content.
 */
final class ExternalContentFile {

  /**
   * The additional data associated with a file.
   */
  protected Data $data;

  /**
   * Constructs a new SourceDocument object.
   *
   * @param string $workingDir
   *   The full (absolute) path to a working dir.
   * @param string $pathname
   *   The full (absolute) path to a file and its name.
   */
  public function __construct(
    protected string $workingDir,
    protected string $pathname,
  ) {
    $this->data = new Data();
  }

  /**
   * Gets a relative (to working dir) pathname.
   *
   * @return string
   *   The relative pathname.
   */
  public function getRelativePathname(): string {
    $without_working_dir = \str_replace(
      $this->getWorkingDir(),
      '',
      $this->getPathname(),
    );

    return \ltrim($without_working_dir, \DIRECTORY_SEPARATOR);
  }

  /**
   * Gets a working dir where file is found.
   *
   * @return string
   *   The working dir path.
   */
  public function getWorkingDir(): string {
    return $this->workingDir;
  }

  /**
   * Gets a file pathname.
   *
   * @return string
   *   The pathname.
   */
  public function getPathname(): string {
    return $this->pathname;
  }

  /**
   * Gets a file extension.
   *
   * @return string
   *   The file extension.
   */
  public function getExtension(): string {
    return \pathinfo($this->getPathname(), \PATHINFO_EXTENSION);
  }

  /**
   * Checks is files is readable.
   *
   * @return bool
   *   TRUE is readable, FALSE if file is not readable or not exists.
   */
  public function isReadable(): bool {
    return \is_readable($this->getPathname());
  }

  /**
   * Gets file contents.
   *
   * @return string
   *   The file contents.
   */
  public function getContents(): string {
    return \file_get_contents($this->getPathname());
  }

  /**
   * Gets the additional data associated with a file.
   *
   * @return \Drupal\external_content\Data\Data
   *   The data storage.
   */
  public function getData(): Data {
    return $this->data;
  }

}
