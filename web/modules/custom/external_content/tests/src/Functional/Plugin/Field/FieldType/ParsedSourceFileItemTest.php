<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Functional\Plugin\Field\FieldType;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\external_content\Data\ParsedSourceFile;
use Drupal\external_content\Data\SourceFile;
use Drupal\external_content\Data\SourceFileContent;
use Drupal\external_content\Data\SourceFileParams;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\external_content\Functional\ExternalContentTestBase;

/**
 * Tests 'external_content_parsed_source_file' field type.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\Field\FieldType\ParsedSourceFileItem
 */
final class ParsedSourceFileItemTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['entity_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that field works as expected.
   */
  public function testField(): void {
    $field_name = \mb_strtolower($this->randomMachineName());
    $field_storage = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'entity_test',
      'type' => 'external_content_parsed_source_file',
    ]);
    $field_storage->save();

    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => $this->randomMachineName() . '_label',
    ])->save();

    $entity = EntityTest::create(['bundle' => 'entity_test']);

    self::assertTrue($entity->hasField($field_name));
    self::assertTrue($entity->get($field_name)->isEmpty());

    $parsed_source_file = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams([]),
      new SourceFileContent(),
    );
    $entity->set($field_name, $parsed_source_file);
    $entity->save();

    self::assertFalse($entity->get($field_name)->isEmpty());

    $parsed_source_file_from_field = $entity->get($field_name)
      ->first()
      ->get('value')
      ->getParsedSourceFile();
    self::assertEquals($parsed_source_file, $parsed_source_file_from_field);
  }

}
