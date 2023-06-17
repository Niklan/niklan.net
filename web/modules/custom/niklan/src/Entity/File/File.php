<?php declare(strict_types = 1);

namespace Drupal\niklan\Entity\File;

use Drupal\file\Entity\File as CoreFile;

/**
 * Provides a bundle class for 'file' entity.
 */
final class File extends CoreFile implements FileInterface {

  /**
   * {@inheritdoc}
   */
  public function getChecksum(): ?string {
    if ($this->get('niklan_checksum')->isEmpty()) {
      return NULL;
    }

    return $this->get('niklan_checksum')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function calculateChecksum(): void {
    if (!\file_exists($this->getFileUri())) {
      return;
    }

    $checksum = \md5_file($this->getFileUri());
    $this->set('niklan_checksum', $checksum);
  }

}
