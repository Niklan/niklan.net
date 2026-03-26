<?php

declare(strict_types=1);

namespace Drupal\app_platform\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

#[FieldType(
  id: self::ID,
  label: new TranslatableMarkup('Software compatibility'),
  description: new TranslatableMarkup('Stores software name and optional version constraint.'),
  no_ui: TRUE,
)]
final class SoftwareCompatibilityItem extends FieldItemBase {

  public const string ID = 'software_compatibility';

  #[\Override]
  public function isEmpty(): bool {
    $name = $this->get('name')->getValue();

    return $name === NULL || $name === '';
  }

  #[\Override]
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Software name'))
      ->setRequired(TRUE);

    $properties['constraint'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Version constraint'));

    return $properties;
  }

  #[\Override]
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'name' => [
          'type' => 'varchar',
          'length' => 128,
          'not null' => TRUE,
        ],
        'constraint' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
    ];
  }

  #[\Override]
  public static function mainPropertyName(): string {
    return 'name';
  }

}
