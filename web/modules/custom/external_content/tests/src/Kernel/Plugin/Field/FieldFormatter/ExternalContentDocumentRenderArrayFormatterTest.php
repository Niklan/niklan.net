<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\Field\FieldFormatter;

use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for render array formatter.
 *
 * @covers \Drupal\external_content\Plugin\Field\FieldFormatter\ExternalContentDocumentRenderArrayFormatter
 * @group external_content
 */
final class ExternalContentDocumentRenderArrayFormatterTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_test',
    'field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installEntitySchema('entity_test');
    $field_storage = $this
      ->entityTypeManager
      ->getStorage('field_storage_config')
      ->create([
        'field_name' => 'external_content',
        'type' => 'external_content_document',
        'entity_type' => 'entity_test',
        'cardinality' => 1,
      ]);
    $field_storage->save();

    $this
      ->entityTypeManager
      ->getStorage('field_config')
      ->create([
        'field_storage' => $field_storage,
        'label' => 'External Content',
        'bundle' => 'entity_test',
        'settings' => [
          'environment' => 'foo',
        ],
      ])
      ->save();
  }

  /**
   * {@selfdoc}
   */
  public function testFormatter(): void {
    // @todo Add tests.
  }

}
