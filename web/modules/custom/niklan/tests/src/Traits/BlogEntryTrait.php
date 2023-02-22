<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Traits;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;

/**
 * Provides a trait with helpers related to blog entry node type.
 */
trait BlogEntryTrait {

  /**
   * Set up blog entry and everything related to it.
   */
  protected function setUpBlogEntry(): void {
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');

    DateFormat::create([
      'id' => 'fallback',
      'pattern' => 'D, m/d/Y - H:i',
    ])->save();

    NodeType::create([
      'type' => 'blog_entry',
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
      'bundle' => 'blog_entry',
      'label' => 'Tags',
    ])->save();
  }

  /**
   * Creates a blog entry entity.
   *
   * @param array $values
   *   An array with entity values.
   *
   * @return \Drupal\node\NodeInterface
   *   The node entity.
   */
  protected function createBlogEntry(array $values = []): NodeInterface {
    $default_values = [
      'type' => 'blog_entry',
      'title' => $this->randomString(),
      'status' => NodeInterface::PUBLISHED,
    ];

    return Node::create($values + $default_values);
  }

}
