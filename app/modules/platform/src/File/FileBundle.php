<?php

declare(strict_types=1);

namespace Drupal\app_platform\File;

use Drupal\app_contract\Contract\File\File;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\file\Entity\File as CoreFile;

final class FileBundle extends CoreFile implements File {

  #[\Override]
  public function getChecksum(): ?string {
    if ($this->get('niklan_checksum')->isEmpty()) {
      return NULL;
    }

    return $this->get('niklan_checksum')->getString();
  }

  #[\Override]
  public function calculateChecksum(): void {
    \assert(\is_string($this->getFileUri()));
    $checksum = FileHelper::checksum($this->getFileUri());
    $this->set('niklan_checksum', $checksum);
  }

  #[\Override]
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);

    $this->calculateChecksum();
  }

}
