<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\external_content\Field\ExternalContentComputedProperty;

/**
 * Provides a field for external content document storage.
 *
 * @FieldType(
 *   id = "external_content",
 *   label = @Translation("External content"),
 *   description = @Translation("Stores the external content."),
 *   category = @Translation("External content"),
 *   default_formatter = "external_content_render_array",
 * )
 */
final class ExternalContentFieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Serialized external content document'))
      ->addConstraint('ExternalContentValidJson')
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

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getConstraints(): array {
    $constraints = parent::getConstraints();
    $constraints['data']['ExternalContentValidJson'] = [];

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'value' => [
          'type' => 'json',
          'pgsql_type' => 'json',
          'mysql_type' => 'json',
          'sqlite_type' => 'text',
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

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return match ($this->get('value')->getValue()) {
      NULL, '', '{}' => TRUE,
      default => FALSE,
    };
  }

}
