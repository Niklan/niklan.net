<?php

declare(strict_types=1);

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
    protected ?Data $data = new Data(),
  ) {}

  /**
   * Checks is files is readable.
   */
  public function isReadable(): bool {
    return \is_readable($this->getPathname());
  }

  /**
   * Gets a file pathname.
   */
  public function getPathname(): string {
    return $this->pathname;
  }

  public function getBasename(): string {
    return \basename($this->getPathname());
  }

  #[\Override]
  public function contents(): string {
    return \file_get_contents($this->getPathname());
  }

  #[\Override]
  public function type(): string {
    return $this->type;
  }

  #[\Override]
  public function data(): Data {
    $this->data->set('type', $this->type());
    $this->data->set('working_dir', $this->getWorkingDir());
    $this->data->set('pathname', $this->getPathname());
    $this->data->set('relative_pathname', $this->getRelativePathname());
    $this->data->set('extension', $this->getExtension());

    return $this->data;
  }

  /**
   * Gets a working dir where file is found.
   */
  public function getWorkingDir(): string {
    return $this->workingDir;
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
   * Gets a file extension.
   */
  public function getExtension(): string {
    return \pathinfo($this->getPathname(), \PATHINFO_EXTENSION);
  }

}
