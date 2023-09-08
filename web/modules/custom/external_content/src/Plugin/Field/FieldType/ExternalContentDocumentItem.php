<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\external_content\Field\ExternalContentDocumentComputed;

/**
 * Provides a field for external content document storage.
 *
 * @FieldType(
 *   id = "external_content_document",
 *   label = @Translation("External content document"),
 *   description = @Translation("Stores the external content document."),
 *   category = @Translation("External content"),
 *   default_formatter = "external_content_document",
 * )
 */
final class ExternalContentDocumentItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Serialized external content document'))
      ->addConstraint('ExternalContentValidJson');

    $properties['document'] = DataDefinition::create('any')
      ->setLabel(new TranslatableMarkup('External content document'))
      ->setDescription(new TranslatableMarkup('The external content document instance.'))
      ->setComputed(TRUE)
      ->setClass(ExternalContentDocumentComputed::class);

    return $properties;
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
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $value = $this->get('value')->getValue();

    return $value === NULL || $value === '';
  }

}
