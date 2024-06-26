<?php declare(strict_types = 1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a basie fields for entities.
 */
final class BaseFieldInfo {

  /**
   * Implements hook_entity_base_field_info().
   */
  public function __invoke(EntityTypeInterface $entity_type): array {
    return match ($entity_type->id()) {
      'file' => $this->file(),
      default => [],
    };
  }

  /**
   * Adds base fields for 'file' entity type.
   *
   * @return array
   *   The base field definitions.
   */
  protected function file(): array {
    $fields = [];

    $fields['niklan_checksum'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('The file checksum'))
      ->setDescription(new TranslatableMarkup('The file MD5 checksum.'))
      ->setSetting('max_length', 255);

    return $fields;
  }

}
