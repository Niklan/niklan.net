<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Field\FieldFormatter;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Provides a test for 'niklan_attached_files' field formatter.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Field\FieldFormatter\MediaAttachedFilesFormatter
 */
final class MediaAttachedFilesFormatterTest extends NiklanTestBase {

  use MediaTypeCreationTrait;
  use TestFileCreationTrait;
  use BlogEntryTrait;

  /**
   * Tests that formatter works as expected.
   */
  public function testFormatter(): void {
    $this
      ->container
      ->get('entity_display.repository')
      ->getViewDisplay('node', 'blog_entry', 'default')
      ->setComponent('field_media_reference', [
        'type' => 'niklan_attached_files',
      ])
      ->save();

    $file = File::create([
      'uri' => \current($this->getTestFiles('text'))->uri,
    ]);
    self::assertEquals(\SAVED_NEW, $file->save());

    $media_image = Media::create([
      'bundle' => 'file',
      'name' => 'Test file',
      'field_media_file' => $file->id(),
    ]);
    self::assertEquals(\SAVED_NEW, $media_image->save());

    $node = Node::create([
      'type' => 'blog_entry',
      'title' => $this->randomMachineName(),
      'field_media_reference' => [
        ['target_id' => $media_image->id()],
      ],
    ]);
    self::assertEquals(\SAVED_NEW, $node->save());

    $build = $this
      ->container
      ->get('entity_type.manager')
      ->getViewBuilder('node')
      ->view($node, 'default');
    $this->render($build);

    self::assertCount(1, $this->cssSelect('a.attached-file'));
    self::assertCount(1, $this->cssSelect('.attached-file__filename'));
    self::assertRaw($media_image->label());
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('file');
    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('media');
    $this->createMediaType('file', ['id' => 'file']);
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
  }

}
