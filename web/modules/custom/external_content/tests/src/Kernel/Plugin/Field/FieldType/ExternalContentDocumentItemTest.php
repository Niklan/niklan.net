<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\Field\FieldType;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content document field type.
 *
 * @covers \Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem
 * @covers \Drupal\external_content\Field\ExternalContentDocumentComputed
 * @group external_content
 */
final class ExternalContentDocumentItemTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'user',
    'entity_test',
    'external_content_test',
  ];

  /**
   * {@selfdoc}
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@selfdoc}
   */
  public function testField(): void {
    $field_storage_config_storage = $this
      ->entityTypeManager
      ->getStorage('field_storage_config');
    $field_config_storage = $this
      ->entityTypeManager
      ->getStorage('field_config');
    $entity_test_storage = $this->entityTypeManager->getStorage('entity_test');

    $field_name = \mb_strtolower($this->randomMachineName());
    $field_storage_config_storage
      ->create([
        'entity_type' => 'entity_test',
        'type' => 'external_content_document',
        'field_name' => $field_name,
      ])->save();
    $field_config_storage
      ->create([
        'entity_type' => 'entity_test',
        'bundle' => 'entity_test',
        'field_name' => $field_name,
      ])->save();

    $document_json = $this->prepareDocumentJson();

    $entity = $entity_test_storage->create();
    \assert($entity instanceof EntityTest);
    $entity->set($field_name, [
      'value' => $document_json,
      'environment_plugin_id' => 'field_item',
    ]);
    $field_item = $entity->get($field_name)->first();
    \assert($field_item instanceof ExternalContentDocumentItem);

    self::assertSame($document_json, $field_item->get('value')->getValue());

    $computed_document = $field_item->get('document')->getValue();

    self::assertEquals(
      $this->prepareDocument(),
      $computed_document,
    );
    // Make sure that consecutive call for document returns cached instance.
    self::assertSame(
      $computed_document,
      $field_item->get('document')->getValue(),
    );
    self::assertCount(0, $field_item->validate());

    // Wrong JSON, but valid plugin.
    $entity->set($field_name, [
      'value' => 'not a json',
      'environment_plugin_id' => 'field_item',
    ]);
    $field_item = $entity->get($field_name)->first();
    \assert($field_item instanceof ExternalContentDocumentItem);

    self::assertEquals('not a json', $field_item->get('value')->getValue());
    self::assertNull($field_item->get('document')->getValue());
    self::assertCount(1, $field_item->validate());

    // Valid JSON, but invalid plugin.
    $entity->set($field_name, [
      'value' => $document_json,
      'environment_plugin_id' => 'random_plugin_that_does_not_exists',
    ]);
    $field_item = $entity->get($field_name)->first();
    \assert($field_item instanceof ExternalContentDocumentItem);

    self::assertCount(1, $field_item->validate());
  }

  /**
   * {@selfdoc}
   */
  private function prepareDocument(): ExternalContentDocument {
    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);
    $p = new HtmlElement('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new HtmlElement('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));
    $document->addChild($p);

    return $document;
  }

  /**
   * {@selfdoc}
   */
  private function prepareDocumentJson(): string {
    $environment = new Environment();
    $environment->addExtension(new BasicHtmlExtension());

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    return $serializer->serialize($this->prepareDocument());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test');

    $this->entityTypeManager = $this->container->get('entity_type.manager');
  }

}
