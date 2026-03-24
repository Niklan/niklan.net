<?php

declare(strict_types=1);

namespace Drupal\app_file\Hook\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslationInterface;

#[Hook('entity_base_field_info')]
final class BaseFieldInfo {

  public function __construct(
    private readonly TranslationInterface $stringTranslation,
  ) {}

  public function __invoke(EntityTypeInterface $entity_type): array {
    return match ($entity_type->id()) {
      'file' => $this->file(),
      default => [],
    };
  }

  protected function file(): array {
    $fields = [];

    $fields['niklan_checksum'] = BaseFieldDefinition::create('string')
      ->setLabel($this->stringTranslation->translate('The file checksum'))
      ->setDescription($this->stringTranslation->translate('The file MD5 checksum.'))
      ->setSetting('max_length', 255);

    return $fields;
  }

}
