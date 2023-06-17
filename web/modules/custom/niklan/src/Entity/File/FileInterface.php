<?php declare(strict_types = 1);

namespace Drupal\niklan\Entity\File;

use Drupal\file\FileInterface as CoreFileInterface;

/**
 * Provides an interface for 'file' entity bundle class.
 */
interface FileInterface extends CoreFileInterface {

  /**
   * Gets the file checksum.
   *
   * @return string|null
   *   The file checksum. NULL if file doesn't exist.
   */
  public function getChecksum(): ?string;

  /**
   * Calculates file checksum.
   */
  public function calculateChecksum(): void;

}
