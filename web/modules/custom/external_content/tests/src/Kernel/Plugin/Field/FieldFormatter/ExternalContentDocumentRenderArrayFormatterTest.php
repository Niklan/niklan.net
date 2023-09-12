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
use Drupal\external_content\Plugin\Field\FieldFormatter\ExternalContentDocumentRenderArrayFormatter;
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
   *
   * @dataProvider formatterData
   */
  public function testFormatter(?string $environment_plugin_id, ?string $document_json, bool $expect_result): void {
    $this->setFormatterSettings(['environment' => $environment_plugin_id]);
    $entity = $this->getEntityTestStorage()->create([
      'external_content' => $document_json,
    ]);

    self::assertEquals(\SAVED_NEW, $entity->save());

    $build = $this->getEntityTestViewBuilder()->view($entity, 'default');
    $this->render($build);

    if ($expect_result) {
      self::assertRaw('<p foo="bar">Hello, World! Formatter is here!</p>');
    }
    else {
      self::assertNoRaw('<p foo="bar">Hello, World! Formatter is here!</p>');
    }
  }

  /**
   * {@selfdoc}
   */
  public function formatterData(): \Generator {
    yield 'Valid result' => [
      'environment_plugin_id' => 'foo',
      'document' => $this->getExternalContentDocumentValue(),
      'expect_result' => TRUE,
    ];

    yield 'Environment is not set' => [
      'environment_plugin_id' => NULL,
      'document' => $this->getExternalContentDocumentValue(),
      'expect_result' => FALSE,
    ];

    yield 'Not existing environment plugin' => [
      'environment_plugin_id' => 'not_exists_for_sure',
      'document' => $this->getExternalContentDocumentValue(),
      'expect_result' => FALSE,
    ];

    yield 'Wrong JSON' => [
      'environment_plugin_id' => 'foo',
      'document' => 'abc',
      'expect_result' => FALSE,
    ];
  }

  /**
   * {@selfdoc}
   *
   * @dataProvider settingsSummaryData
   */
  public function testSettingsSummary(array $settings, array $expected_summary): void {
    $this->setFormatterSettings($settings);

    $display_repository = $this->container->get('entity_display.repository');
    $display = $display_repository
      ->getViewDisplay('entity_test', 'entity_test');
    $formatter = $display->getRenderer('external_content');
    \assert($formatter instanceof ExternalContentDocumentRenderArrayFormatter);
    $actual_summary = \array_map('strval', $formatter->settingsSummary());

    self::assertSame($expected_summary, $actual_summary);
  }

  /**
   * {@selfdoc}
   */
  public function settingsSummaryData(): \Generator {
    yield 'No environment selected' => [
      'settings' => ['environment' => NULL],
      'expected_summary' => ['Environment: none'],
    ];

    yield 'Foo environment selected' => [
      'settings' => ['environment' => 'foo'],
      'expected_summary' => ['Environment: Foo environment (foo)'],
    ];
  }

}
