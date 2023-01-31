<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'external_content_parsed_source_file' field type.
 *
 * @FieldType(
 *   id = "external_content_parsed_source_file",
 *   label = @Translation("Parsed source file"),
 *   category = @Translation("External Content"),
 *   no_ui = TRUE,
 * )
 */
final class ParsedSourceFileItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['value'] = DataDefinition::create('external_content_parsed_source_file')
      ->setLabel((string) new TranslatableMarkup('Parsed source file'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'value' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
      ],
    ];
  }

}