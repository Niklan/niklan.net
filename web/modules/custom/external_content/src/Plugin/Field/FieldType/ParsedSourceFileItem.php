<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\external_content\Data\ParsedSourceFile;
use Drupal\external_content\Plugin\DataType\ParsedSourceFile as ParsedSourceFileDataType;

/**
 * Defines the 'external_content_parsed_source_file' field type.
 *
 * @FieldType(
 *   id = "external_content_parsed_source_file",
 *   label = @Translation("Parsed source file"),
 *   category = @Translation("External Content"),
 *   default_formatter = "external_content_rendered_parsed_source_file",
 *   list_class = "\Drupal\external_content\Field\ParsedSourceFileItemList",
 * )
 */
final class ParsedSourceFileItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $data_type = 'external_content_parsed_source_file';
    $properties['value'] = DataDefinition::create($data_type)
      ->setLabel((string) new TranslatableMarkup('Parsed source file'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * Gets parsed source file.
   */
  public function getParsedSourceFile(): ?ParsedSourceFile {
    $value = $this->get('value');
    \assert($value instanceof ParsedSourceFileDataType);

    return $value->getParsedSourceFile();
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
