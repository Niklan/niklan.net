<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\ExtraField\Display\Term\Tag;

use Drupal\Tests\niklan\Kernel\Plugin\ExtraField\ExtraFieldTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;
use Drupal\Tests\niklan\Traits\TagsTrait;

/**
 * Provides a test for statistics extra field.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\ExtraField\Display\Term\Tag\Statistics
 */
final class StatisticsTest extends ExtraFieldTestBase {

  use BlogEntryTrait;
  use TagsTrait;

  /**
   * Test that field works as expected when no articles found.
   */
  public function testViewWithoutArticles(): void {
    $tag = $this->createTag(['name' => 'Tag A']);
    $tag->save();

    $plugin = $this->createExtraFieldDisplayInstance(
      'niklan_taxonomy_tag_statistics',
    );
    $plugin->setEntity($tag);
    $build = $plugin->view($tag);

    self::assertSame([], $build);
  }

  /**
   * Test that field works as expected when no articles found.
   */
  public function testView(): void {
    $tag = $this->createTag(['name' => 'Tag A']);
    $tag->save();

    $this
      ->createBlogEntry([
        'title' => 'Blog A',
        'field_tags' => [['target_id' => $tag->id()]],
        'created' => 100_000_000,
      ])
      ->save();

    $this
      ->createBlogEntry([
        'title' => 'Blog B',
        'field_tags' => [['target_id' => $tag->id()]],
        'created' => 200_000_000,
      ])
      ->save();

    $plugin = $this->createExtraFieldDisplayInstance(
      'niklan_taxonomy_tag_statistics',
    );
    $plugin->setEntity($tag);
    $build = $plugin->view($tag);
    $this->render($build);

    self::assertRaw('2 publications from 3 March 1973 to 4 May 1976');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
    $this->setUpTagsVocabulary();
  }

}
