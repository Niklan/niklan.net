<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\Field\FieldType;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\external_content\Dto\HtmlElement;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\PlainTextElement;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Tests 'external_content_rendered_parsed_source_file' field formatter.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\Field\FieldFormatter\RenderedParsedSourceFileFormatter
 */
final class RenderedParsedSourceFileFormatterTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'entity_test',
    'user',
    'system',
  ];

  /**
   * The field name for testing.
   */
  protected string $fieldName;

  /**
   * The entity display for testing.
   */
  protected EntityViewDisplayInterface $display;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['field']);
    $this->installEntitySchema('entity_test');

    $this->fieldName = \mb_strtolower($this->randomMachineName());

    $field_storage = FieldStorageConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => 'entity_test',
      'type' => 'external_content_parsed_source_file',
    ]);
    $field_storage->save();

    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();

    $this->display = $this
      ->container
      ->get('entity_display.repository')
      ->getViewDisplay('entity_test', 'entity_test')
      ->setComponent($this->fieldName, [
        'type' => 'external_content_rendered_parsed_source_file',
        'settings' => [],
      ]);
    $this->display->save();
  }

  /**
   * Tests that field formatter works as expected.
   */
  public function testFormatter(): void {
    $content = new SourceFileContent();
    $paragraph = new HtmlElement('p', ['data-foo' => 'bar']);
    $paragraph->addChild(new PlainTextElement('Hello, World! '));
    $link = new HtmlElement('a', ['href' => 'https://example.com']);
    $link->addChild(new PlainTextElement('This is a link'));
    $paragraph->addChild($link);
    $paragraph->addChild(new PlainTextElement(' inside a paragraph.'));
    $content->addChild($paragraph);

    $parsed_source_file = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'a', 'language' => 'en']),
      $content,
    );

    $entity = EntityTest::create([]);
    $entity->set($this->fieldName, $parsed_source_file);
    $content = $this->display->build($entity);
    $this->render($content);

    self::assertRaw(
      '<p data-foo="bar">Hello, World! <a href="https://example.com">This is a link</a>',
    );
  }

}
