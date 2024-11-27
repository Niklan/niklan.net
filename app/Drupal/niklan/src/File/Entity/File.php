<?php

declare(strict_types=1);

namespace Drupal\niklan\File\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\file\Entity\File as CoreFile;
use Drupal\niklan\File\Utils\FileHelper;

final class File extends CoreFile implements FileInterface {

  #[\Override]
  public function getChecksum(): ?string {
    if ($this->get('niklan_checksum')->isEmpty()) {
      return NULL;
    }

    return $this->get('niklan_checksum')->getString();
  }

  #[\Override]
  public function calculateChecksum(): void {
    $checksum = FileHelper::checksum($this->getFileUri());
    $this->set('niklan_checksum', $checksum);
  }

  #[\Override]
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);

    $this->calculateChecksum();
  }

}
