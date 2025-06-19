<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\external_content\Field\ExternalContentComputedProperty;
use Drupal\external_content\Plugin\Validation\Constraint\ExternalContentValidJsonConstraint;

#[FieldType(
  id: self::ID,
  label: new TranslatableMarkup('External content'),
  description: new TranslatableMarkup('Stores the external content.'),
  category: 'external_content',
)]
final class ExternalContentFieldItem extends FieldItemBase {

  public const string ID = 'external_content';

  #[\Override]
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    $constraint_manager = $this
      ->getTypedDataManager()
      ->getValidationConstraintManager();

    $constraints[] = $constraint_manager->create('ComplexData', [
      'value' => [
        ExternalContentValidJsonConstraint::ID => [],
      ],
      'data' => [
        ExternalContentValidJsonConstraint::ID => [],
      ],
    ]);

    return $constraints;
  }

  #[\Override]
  public function isEmpty(): bool {
    return match ($this->get('value')->getValue()) {
      NULL, '', '{}' => TRUE,
      default => FALSE,
    };
  }

  #[\Override]
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Serialized external content document'))
      ->setRequired(TRUE);

    $properties['environment_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Environment ID'))
      ->setDescription(new TranslatableMarkup('The environment ID defined in the service container.'))
      ->setRequired(TRUE);

    $properties['data'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Data'))
      ->setDescription(new TranslatableMarkup('A JSON string with additional data.'));

    $properties['content'] = DataDefinition::create('any')
      ->setLabel(new TranslatableMarkup('External content'))
      ->setDescription(new TranslatableMarkup('The external content element instance.'))
      ->setComputed(TRUE)
      ->setClass(ExternalContentComputedProperty::class);

    return $properties;
  }

  #[\Override]
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        // The content can be big enough and JSON type has limitations which can
        // be exceeded even on a small content, but with a complex structure.
        // All the information for which JSON search and manipulation wanted to
        // be used, should be saved in 'data'.
        //
        // @see https://github.com/mysql/mysql-server/blame/4869291f7ee258e136ef03f5a50135fe7329ffb9/sql/json_syntax_check.cc#L80
        'value' => [
          'type' => 'text',
          'size' => 'medium',
          'not null' => FALSE,
        ],
        'environment_id' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
        ],
        'data' => [
          'type' => 'json',
          'pgsql_type' => 'json',
          'mysql_type' => 'json',
          'sqlite_type' => 'text',
          'not null' => FALSE,
        ],
      ],
    ];
  }

}
