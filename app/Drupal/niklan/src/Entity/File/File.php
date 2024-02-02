<?php declare(strict_types = 1);

namespace Drupal\niklan\Entity\File;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\file\Entity\File as CoreFile;
use Drupal\niklan\Helper\FileHelper;

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
    $checksum = FileHelper::checksum($this->getFileUri());
    $this->set('niklan_checksum', $checksum);
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);

    $this->calculateChecksum();
  }

}
