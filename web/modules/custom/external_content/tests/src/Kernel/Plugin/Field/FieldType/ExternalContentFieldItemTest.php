<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\Field\FieldType;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Node\Html\PlainText;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\external_content\Serializer\ContentSerializer;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content document field type.
 *
 * @covers \Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem
 * @covers \Drupal\external_content\Field\ExternalContentComputedProperty
 * @group external_content
 */
final class ExternalContentFieldItemTest extends ExternalContentTestBase {

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
  protected string $fieldName;

  /**
   * {@selfdoc}
   */
  public function testValidField(): void {
    $document_json = $this->prepareJson($this->prepareDocument());
    $entity = $this->createEntity();
    $entity->set($this->fieldName, [
      'value' => $document_json,
      'environment_plugin_id' => 'field_item',
    ]);
    $field_item = $entity->get($this->fieldName)->first();
    \assert($field_item instanceof ExternalContentFieldItem);

    self::assertSame($document_json, $field_item->get('value')->getValue());

    $computed_content = $field_item->get('content')->getValue();

    self::assertEquals(
      $this->prepareDocument(),
      $computed_content,
    );
    // Make sure that consecutive call for document returns cached instance.
    self::assertSame(
      $computed_content,
      $field_item->get('content')->getValue(),
    );
    self::assertCount(0, $field_item->validate());
  }

  /**
   * {@selfdoc}
   */
  public function testWrongJsonValidPlugin(): void {
    $entity = $this->createEntity();
    // Wrong JSON, but valid plugin.
    $entity->set($this->fieldName, [
      'value' => 'not a json',
      'environment_plugin_id' => 'field_item',
    ]);
    $field_item = $entity->get($this->fieldName)->first();
    \assert($field_item instanceof ExternalContentFieldItem);

    self::assertEquals('not a json', $field_item->get('value')->getValue());
    self::assertNull($field_item->get('content')->getValue());
    self::assertCount(1, $field_item->validate());
  }

  /**
   * {@selfdoc}
   */
  public function testValidJsonWrongPlugin(): void {
    $document_json = $this->prepareJson($this->prepareDocument());
    $entity = $this->createEntity();
    $entity->set($this->fieldName, [
      'value' => $document_json,
      'environment_plugin_id' => 'random_plugin_that_does_not_exists',
    ]);
    $field_item = $entity->get($this->fieldName)->first();
    \assert($field_item instanceof ExternalContentFieldItem);

    self::assertCount(1, $field_item->validate());
    self::assertNull($field_item->get('content')->getValue());
  }

  /**
   * {@selfdoc}
   */
  public function testValueStartsNotWithExternalContentDocument(): void {
    $element = new PlainText('Hello!');

    $entity = $this->createEntity();
    $entity->set($this->fieldName, [
      'value' => $this->prepareJson($element),
      'environment_plugin_id' => 'field_item',
    ]);
    $field_item = $entity->get($this->fieldName)->first();
    \assert($field_item instanceof ExternalContentFieldItem);

    // This is a valid JSON, but starts with a different node.
    self::assertCount(0, $field_item->validate());
    self::assertEquals($element, $field_item->get('content')->getValue());
  }

  /**
   * {@selfdoc}
   */
  public function testEmptyData(): void {
    $entity = $this->createEntity();

    $entity->set($this->fieldName, [
      'value' => $this->prepareJson(new PlainText('Hello!')),
      'environment_plugin_id' => 'field_item',
    ]);
    $entity->save();

    $field_item = $entity->get($this->fieldName)->first();
    \assert($field_item instanceof ExternalContentFieldItem);

    self::assertNull($field_item->get('data')->getValue());
    self::assertCount(0, $field_item->validate());
  }

  /**
   * {@selfdoc}
   */
  public function testNotEmptyData(): void {
    $entity = $this->createEntity();
    $entity->set($this->fieldName, [
      'value' => $this->prepareJson(new PlainText('Hello!')),
      'environment_plugin_id' => 'field_item',
      'data' => \json_encode(['foo' => 'bar']),
    ]);
    $entity->save();

    $field_item = $entity->get($this->fieldName)->first();
    \assert($field_item instanceof ExternalContentFieldItem);

    self::assertEquals('{"foo":"bar"}', $field_item->get('data')->getValue());
    self::assertCount(0, $field_item->validate());
  }

  /**
   * {@selfdoc}
   */
  private function prepareDocument(): Content {
    $file = new File('foo', 'bar', 'html');
    $document = new Content($file);
    $p = new Element('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new Element('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));
    $document->addChild($p);

    return $document;
  }

  /**
   * {@selfdoc}
   */
  private function prepareJson(NodeInterface $node): string {
    $environment = new Environment();
    $environment->addSerializer(new ContentSerializer());
    $environment->addExtension(new BasicHtmlExtension());

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    return $serializer->normalize($node);
  }

  /**
   * {@selfdoc}
   */
  public function createEntity(): EntityTest {
    $entity_test_storage = $this->entityTypeManager->getStorage('entity_test');
    $entity = $entity_test_storage->create();
    \assert($entity instanceof EntityTest);

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test');

    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $field_storage_config_storage = $this
      ->entityTypeManager
      ->getStorage('field_storage_config');
    $field_config_storage = $this
      ->entityTypeManager
      ->getStorage('field_config');

    $this->fieldName = \mb_strtolower($this->randomMachineName());
    $field_storage_config_storage
      ->create([
        'entity_type' => 'entity_test',
        'type' => 'external_content',
        'field_name' => $this->fieldName,
      ])->save();
    $field_config_storage
      ->create([
        'entity_type' => 'entity_test',
        'bundle' => 'entity_test',
        'field_name' => $this->fieldName,
      ])->save();
  }

}
