<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormState;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\niklan\Plugin\Field\FieldFormatter\MediaResponsiveThumbnailFormatter;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Provides a test for 'niklan_responsive_media_thumbnail' field formatter.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Field\FieldFormatter\MediaResponsiveThumbnailFormatter
 */
final class MediaResponsiveThumbnailFormatterTest extends NiklanTestBase {

  use MediaTypeCreationTrait;
  use TestFileCreationTrait;
  use BlogEntryTrait;

  /**
   * Tests that formatter works without media entities.
   */
  public function testFormatterWithoutMedia(): void {
    $this->setFormatterSettings([
      'responsive_image_style' => 'fallback',
      'image_link' => 'content',
    ]);

    $node = Node::create([
      'type' => 'blog_entry',
      'title' => $this->randomMachineName(),
    ]);
    self::assertEquals(\SAVED_NEW, $node->save());
    $this->renderNode($node);

    self::assertCount(0, $this->cssSelect('article picture'));
  }

  /**
   * Tests that formatter works with link to a content.
   */
  public function testFormatterLinkedToContent(): void {
    $this->setFormatterSettings([
      'responsive_image_style' => 'fallback',
      'image_link' => 'content',
    ]);

    $file = File::create([
      'uri' => \current($this->getTestFiles('image'))->uri,
    ]);
    self::assertEquals(\SAVED_NEW, $file->save());

    $media_image = Media::create([
      'bundle' => 'image',
      'name' => 'Test image',
      'field_media_image' => $file->id(),
    ]);
    self::assertEquals(\SAVED_NEW, $media_image->save());

    $node = Node::create([
      'type' => 'blog_entry',
      'title' => $this->randomMachineName(),
      'field_media_reference' => $media_image,
    ]);
    self::assertEquals(\SAVED_NEW, $node->save());

    $this->renderNode($node);

    self::assertCount(1, $this->cssSelect('article picture img'));
    self::assertCount(1, $this->cssSelect("article a[href='/node/{$node->id()}'] picture"));
    self::assertRaw($file->getFilename());
  }

  /**
   * Tests that formatter works with link to a media.
   */
  public function testFormatterLinkedToMedia(): void {
    $this->setFormatterSettings([
      'responsive_image_style' => 'fallback',
      'image_link' => 'media',
    ]);

    $file = File::create([
      'uri' => \current($this->getTestFiles('image'))->uri,
    ]);
    self::assertEquals(\SAVED_NEW, $file->save());

    $media_image = Media::create([
      'bundle' => 'image',
      'name' => 'Test image',
      'field_media_image' => $file->id(),
    ]);
    self::assertEquals(\SAVED_NEW, $media_image->save());

    $node = Node::create([
      'type' => 'blog_entry',
      'title' => $this->randomMachineName(),
      'field_media_reference' => $media_image,
    ]);
    self::assertEquals(\SAVED_NEW, $node->save());

    $this->renderNode($node);

    self::assertCount(1, $this->cssSelect('article picture img'));
    // By default, canonical link is directing on edit form.
    self::assertCount(1, $this->cssSelect("article a[href='/media/{$media_image->id()}/edit'] picture"));
    self::assertRaw($file->getFilename());
  }

  /**
   * Tests that settings form allows to set up all expected settings.
   */
  public function testSettingsForm(): void {
    $formatter = $this->getFormatterInstance();
    $plugin_form = $formatter->settingsForm([], new FormState());

    self::assertArrayHasKey('image_link', $plugin_form);
    self::assertArrayHasKey('responsive_image_style', $plugin_form);
  }

  /**
   * Tests that settings summary contains all expected summary.
   *
   * @param string|null $setting_name
   *   The setting name.
   * @param string|null $setting_value
   *   The settings value.
   * @param string $expected_summary
   *   The expected summary.
   *
   * @dataProvider settingsSummaryDataProvider
   */
  public function testSettingsSummary(?string $setting_name, ?string $setting_value, string $expected_summary): void {
    $formatter = $this->getFormatterInstance();

    if ($setting_value) {
      $formatter->setSetting($setting_name, $setting_value);
    }

    $summary = $formatter->settingsSummary();

    // They are most likely is translatable strings.
    foreach ($summary as $delta => $value) {
      $summary[$delta] = (string) $value;
    }

    self::assertContains($expected_summary, $summary);
  }

  /**
   * Provides settings summary data for testing.
   */
  public function settingsSummaryDataProvider(): \Generator {
    yield [NULL, NULL, 'Select a responsive image style.'];
    yield ['image_link', 'content', 'Linked to content'];
    yield ['image_link', 'media', 'Linked to media item'];
    yield [
      'responsive_image_style',
      'fallback',
      'Responsive image style: Fallback',
    ];
  }

  /**
   * Updates formatter settings for tested field.
   *
   * @param array $settings
   *   The formatter settings.
   */
  protected function setFormatterSettings(array $settings): void {
    $this
      ->container
      ->get('entity_display.repository')
      ->getViewDisplay('node', 'blog_entry', 'default')
      ->setComponent('field_media_reference', [
        'type' => 'niklan_responsive_media_thumbnail',
        'settings' => $settings,
      ])
      ->save();
  }

  /**
   * Renders a node in default view mode.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   */
  protected function renderNode(NodeInterface $node): void {
    $build = $this
      ->container
      ->get('entity_type.manager')
      ->getViewBuilder('node')
      ->view($node, 'default');
    $this->render($build);
  }

  /**
   * Gets formatter instance.
   */
  protected function getFormatterInstance(): MediaResponsiveThumbnailFormatter {
    $field_definitions = $this
      ->container
      ->get('entity_field.manager')
      ->getFieldDefinitions('node', 'blog_entry');

    return $this
      ->container
      ->get('plugin.manager.field.formatter')
      ->getInstance([
        'configuration' => [
          'type' => 'niklan_responsive_media_thumbnail',
        ],
        'view_mode' => 'full',
        'field_definition' => $field_definitions['field_media_reference'],
      ]);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('file');
    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('media');
    $this->createMediaType('image', ['id' => 'image']);
    $blog_type = $this->setUpBlogEntry();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_media_reference',
      'type' => 'entity_reference',
      'entity_type' => 'node',
      'cardinality' => 1,
      'settings' => [
        'target_type' => 'media',
      ],
    ]);
    $field_storage->save();

    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $blog_type->id(),
      'label' => 'Reference media',
      'translatable' => FALSE,
    ])->save();

    $style = ResponsiveImageStyle::create([
      'id' => 'fallback',
      'label' => 'Fallback',
    ]);
    $style->addImageStyleMapping('test_breakpoint', '1x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'small',
    ]);
    $style->save();
  }

}
