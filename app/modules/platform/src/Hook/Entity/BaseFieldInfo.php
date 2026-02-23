<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Hook('entity_base_field_info')]
final class BaseFieldInfo {

  protected function file(): array {
    $fields = [];

    $fields['niklan_checksum'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('The file checksum'))
      ->setDescription(new TranslatableMarkup('The file MD5 checksum.'))
      ->setSetting('max_length', 255);

    return $fields;
  }

  public function __invoke(EntityTypeInterface $entity_type): array {
    return match ($entity_type->id()) {
      'file' => $this->file(),
      default => [],
    };
  }

}
