<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\niklan\Helper\TagStatistics;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Provides a test for tag statistics helper.
 */
final class TagStatisticsTest extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['text'];

  /**
   * The tag statistics.
   */
  protected TagStatistics $tagStatistics;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->tagStatistics = $this
      ->container
      ->get('niklan.helper.tag_statistics');

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('taxonomy_term');
    $this->installEntitySchema('taxonomy_vocabulary');

    Vocabulary::create(['vid' => 'tags']);
    Term::create([
      'vid' => 'tags',
      'name' => 'Tag A',
    ]);

    Term::create([
      'vid' => 'tags',
      'name' => 'Tag B',
    ]);

    NodeType::create([
      'type' => 'blog',
      'name' => 'Blog post',
    ])->save();

    $field_tags_storage = FieldStorageConfig::create([
      'field_name' => 'field_tags',
      'type' => 'entity_reference',
      'entity_type' => 'node',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      'settings' => [
        'target_type' => 'taxonomy_term',
      ],
    ]);
    $field_tags_storage->save();

    FieldConfig::create([
      'field_storage' => $field_tags_storage,
      'bundle' => 'blog',
      'label' => 'Tags',
    ])->save();
  }

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

    Node::create([
      'type' => 'blog',
      'title' => 'Node A',
      'field_tags' => [
        ['target_id' => $tag_a->id()],
        ['target_id' => $tag_b->id()],
      ],
    ])->save();

    Node::create([
      'type' => 'blog',
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
   * Tests that limitration works as expected.
   */
  public function testLimitedStatistics(): void {
    $tag_a = Term::create(['vid' => 'tags', 'name' => 'Tag A']);
    $tag_a->save();

    $tag_b = Term::create(['vid' => 'tags', 'name' => 'Tag B']);
    $tag_b->save();

    $tag_c = Term::create(['vid' => 'tags', 'name' => 'Tag C']);
    $tag_c->save();

    Node::create([
      'type' => 'blog',
      'title' => 'Node A',
      'field_tags' => [
        ['target_id' => $tag_a->id()],
        ['target_id' => $tag_b->id()],
      ],
    ])->save();

    Node::create([
      'type' => 'blog',
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

}
