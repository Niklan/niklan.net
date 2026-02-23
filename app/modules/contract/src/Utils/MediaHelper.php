<?php

declare(strict_types=1);

namespace Drupal\app_contract\Utils;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;

final readonly class MediaHelper {

  public static function getFileFromMediaField(FieldableEntityInterface $entity, string $field_name): ?FileInterface {
    $media = $entity->get($field_name)->first()?->get('entity')->getValue();
    \assert(\is_null($media) || $media instanceof MediaInterface);

    return self::getFile($media);
  }

  public static function getFile(?MediaInterface $media): ?FileInterface {
    $file_field = $media?->getSource()->getConfiguration()['source_field'];
    $file = $media?->get($file_field)->first()?->get('entity')->getValue();
    \assert(\is_null($file) || $file instanceof FileInterface);

    return $file;
  }

  public static function getFileUri(?MediaInterface $entity): ?string {
    return self::getFile($entity)?->getFileUri();
  }

}
