<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Serializer\Serializer;
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
    'external_content_test',
    'entity_test',
    'field',
    'user',
    'system',
  ];

  /**
   * {@selfdoc}
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installEntitySchema('user');
    $this->installSchema('user', 'users_data');

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
  private function setFormatterSettings(array $settings): void {
    $this
      ->container
      ->get('entity_display.repository')
      ->getViewDisplay('entity_test', 'entity_test', 'default')
      ->setComponent('external_content', [
        'type' => 'external_content_render_array',
        'settings' => $settings,
      ])
      ->save();
  }

  /**
   * {@selfdoc}
   */
  private function getEntityTestStorage(): ContentEntityStorageInterface {
    return $this->entityTypeManager->getStorage('entity_test');
  }

  /**
   * {@selfdoc}
   */
  private function getEntityTestViewBuilder(): EntityViewBuilderInterface {
    return $this->entityTypeManager->getViewBuilder('entity_test');
  }

  /**
   * {@selfdoc}
   */
  private function getExternalContentDocumentValue(): string {
    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);
    $p = new HtmlElement('p', new Attributes(['foo' => 'bar']));
    $p->addChild(new PlainText('Hello, World! Formatter is here!'));
    $document->addChild($p);

    return (new Serializer())->serialize($document);
  }

  /**
   * {@selfdoc}
   */
  public function testFormatterWithoutEnvironment(): void {
    $this->setFormatterSettings(['environment' => NULL]);
    $entity = $this->getEntityTestStorage()->create([
      'external_content' => $this->getExternalContentDocumentValue(),
    ]);
    self::assertEquals(\SAVED_NEW, $entity->save());

    $build = $this->getEntityTestViewBuilder()->view($entity);
    $this->render($build);

    \dump($this->getRawContent());
  }

  /**
   * {@selfdoc}
   */
  public function testFormatter(): void {
    $this->setFormatterSettings(['environment' => 'foo']);
    $entity = $this->getEntityTestStorage()->create([
      'external_content' => $this->getExternalContentDocumentValue(),
    ]);

    self::assertEquals(\SAVED_NEW, $entity->save());

    $build = $this->getEntityTestViewBuilder()->view($entity, 'default');
    $this->render($build);

    \dump($this->getRawContent());
  }

}
