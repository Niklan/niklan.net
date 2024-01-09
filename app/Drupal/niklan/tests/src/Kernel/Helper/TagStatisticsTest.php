<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel;

use Drupal\niklan\Helper\TagStatistics;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;
use Drupal\Tests\niklan\Traits\TagsTrait;

/**
 * Provides a test for tag statistics helper.
 */
final class TagStatisticsTest extends NiklanTestBase {

  use BlogEntryTrait;
  use TagsTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['text'];

  /**
   * The tag statistics.
   */
  protected TagStatistics $tagStatistics;

  /**
   * Tests statistics for an empty result.
   */
  public function testEmptyStatistics(): void {
    $result = $this->tagStatistics->getBlogEntryUsage();
    self::assertEmpty($result);
  }

  /**
   * Tests statistics with existed content.
   */
  public function testStatistics(): void {
    $tag_a = Term::create(['vid' => 'tags', 'name' => 'Tag A']);
    $tag_a->save();

    $tag_b = Term::create(['vid' => 'tags', 'name' => 'Tag B']);
    $tag_b->save();

    $tag_c = Term::create(['vid' => 'tags', 'name' => 'Tag C']);
    $tag_c->save();

    $this->createBlogEntry([
      'title' => 'Node A',
      'field_tags' => [
        ['target_id' => $tag_a->id()],
        ['target_id' => $tag_b->id()],
      ],
    ])->save();

    $this->createBlogEntry([
      'title' => 'Node B',
      'field_tags' => [
        ['target_id' => $tag_b->id()],
      ],
    ])->save();

    $result = $this->tagStatistics->getBlogEntryUsage();

    // Tag A.
    self::assertEquals('1', $result[1]->tid);
    self::assertEquals('1', $result[1]->count);

    // Tag B.
    self::assertEquals('2', $result[2]->tid);
    self::assertEquals('2', $result[2]->count);

    // Tag C.
    self::assertEquals('3', $result[3]->tid);
    self::assertEquals('0', $result[3]->count);
  }

  /**
   * Tests that limitation works as expected.
   */
  public function testLimitedStatistics(): void {
    $tag_a = Term::create(['vid' => 'tags', 'name' => 'Tag A']);
    $tag_a->save();

    $tag_b = Term::create(['vid' => 'tags', 'name' => 'Tag B']);
    $tag_b->save();

    $tag_c = Term::create(['vid' => 'tags', 'name' => 'Tag C']);
    $tag_c->save();

    $this->createBlogEntry([
      'title' => 'Node A',
      'field_tags' => [
        ['target_id' => $tag_a->id()],
        ['target_id' => $tag_b->id()],
      ],
    ])->save();

    $this->createBlogEntry([
      'title' => 'Node B',
      'field_tags' => [
        ['target_id' => $tag_b->id()],
      ],
    ])->save();

    $result = $this->tagStatistics->getBlogEntryUsage(1);

    // Tag B - because it has more usages and limited to a singled result.
    self::assertEquals('2', $result[2]->tid);
    self::assertEquals('2', $result[2]->count);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
    $this->setUpTagsVocabulary();

    $this->tagStatistics = $this
      ->container
      ->get('niklan.helper.tag_statistics');
  }

}
