<?php declare(strict_types = 1);

namespace Drupal\external_content\Source;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Data;

/**
 * Represents a source file with a content.
 */
final class File implements SourceInterface {

  /**
   * Constructs a new ExternalContentFile object.
   */
  public function __construct(
    protected string $workingDir,
    protected string $pathname,
    protected string $type,
    protected ?Data $data = NULL,
  ) {
    $this->data ??= new Data();
  }

  /**
   * Gets a relative (to working dir) pathname.
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
   */
  public function getWorkingDir(): string {
    return $this->workingDir;
  }

  /**
   * Gets a file pathname.
   */
  public function getPathname(): string {
    return $this->pathname;
  }

  /**
   * Gets a file extension.
   */
  public function getExtension(): string {
    return \pathinfo($this->getPathname(), \PATHINFO_EXTENSION);
  }

  /**
   * Checks is files is readable.
   */
  public function isReadable(): bool {
    return \is_readable($this->getPathname());
  }

  /**
   * {@inheritdoc}
   */
  public function contents(): string {
    return \file_get_contents($this->getPathname());
  }

  /**
   * {@inheritdoc}
   */
  public function type(): string {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function data(): Data {
    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return $this->getRelativePathname();
  }

}
