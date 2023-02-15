<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Functional\Plugin\ExternalContent\Loader;

use Drupal\external_content\Plugin\ExternalContent\Loader\LoaderInterface;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\external_content\Dto\ExternalContent;
use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Plugin\ExternalContent\Loader\LoaderPluginManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\external_content\Functional\ExternalContentTestBase;

/**
 * Provides test for 'entity_test_loader' loader plugin.
 *
 * @coversDefaultClass \Drupal\external_content_test\Plugin\ExternalContent\Loader\EntityTestLoader
 */
final class EntityTestLoaderTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The 'entity_test' storage.
   */
  protected ?ContentEntityStorageInterface $entityTestStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $external_content_field_storage = FieldStorageConfig::create([
      'field_name' => 'external_content',
      'entity_type' => 'entity_test',
      'type' => 'external_content_parsed_source_file',
    ]);
    $external_content_field_storage->save();
    FieldConfig::create([
      'field_storage' => $external_content_field_storage,
      'bundle' => 'entity_test',
      'label' => 'External content',
    ])->save();

    $external_id_field_storage = FieldStorageConfig::create([
      'field_name' => 'external_id',
      'entity_type' => 'entity_test',
      'type' => 'string',
    ]);
    $external_id_field_storage->save();
    FieldConfig::create([
      'field_storage' => $external_id_field_storage,
      'bundle' => 'entity_test',
      'label' => 'External ID',
    ])->save();

    $this->entityTestStorage = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('entity_test');
  }

  /**
   * Tests the load method.
   */
  public function testLoad(): void {
    $external_content_a = new ExternalContent('foo');
    $external_content_a->addTranslation('en', new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams([
        'id' => 'foo',
        'language' => 'en',
        'title' => 'This is a foo title',
      ]),
      new SourceFileContent(),
    ));

    $external_content_b = new ExternalContent('bar');
    $external_content_b->addTranslation('en', new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams([
        'id' => 'bar',
        'language' => 'en',
        'title' => 'This is a bar title',
      ]),
      new SourceFileContent(),
    ));

    $collection = new ExternalContentCollection();
    $collection->add($external_content_a);
    $collection->add($external_content_b);

    $count = $this
      ->entityTestStorage
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    self::assertEquals(0, $count);

    $loader_instance = $this
      ->container
      ->get(LoaderPluginManagerInterface::class)
      ->createInstance('entity_test_loader');
    \assert($loader_instance instanceof LoaderInterface);
    $loader_instance->load($collection);

    $count = $this
      ->entityTestStorage
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    self::assertEquals(2, $count);

    $entity_a_id = $this
      ->entityTestStorage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('external_id', 'foo')
      ->execute();
    $entity_a = $this->entityTestStorage->load(\reset($entity_a_id));
    self::assertEquals('This is a foo title', $entity_a->getName());

    $entity_b_id = $this
      ->entityTestStorage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('external_id', 'bar')
      ->execute();
    $entity_b = $this->entityTestStorage->load(\reset($entity_b_id));
    self::assertEquals('This is a bar title', $entity_b->getName());

    // Update information.
    $external_content_a = new ExternalContent('foo');
    $external_content_a->addTranslation('en', new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams([
        'id' => 'foo',
        'language' => 'en',
        'title' => 'The updated foo title',
      ]),
      new SourceFileContent(),
    ));
    $collection = new ExternalContentCollection();
    $collection->add($external_content_a);
    $loader_instance->load($collection);
    $entity_a = $this->entityTestStorage->load(\reset($entity_a_id));
    self::assertEquals('The updated foo title', $entity_a->getName());
  }

}
