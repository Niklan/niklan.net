<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Traits;

use Drupal\comment\CommentInterface;
use Drupal\comment\Entity\Comment;
use Drupal\comment\Entity\CommentType;
use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\NodeInterface;

/**
 * Provides a helper trait for blog entry comments.
 */
trait BlogEntryCommentTrait {

  /**
   * Sets up blog entry comment type.
   */
  protected function setUpBlogEntryComment(): void {
    $this->installEntitySchema('user');
    $this->installEntitySchema('comment');

    DateFormat::create([
      'id' => 'fallback',
      'pattern' => 'D, m/d/Y - H:i',
    ])->save();

    CommentType::create([
      'id' => 'comment_node_blog_entry',
      'label' => 'Blog article comment',
      'target_entity_type_id' => 'node',
    ])->save();

    $comment_body = FieldStorageConfig::create([
      'field_name' => 'comment_body',
      'entity_type' => 'comment',
      'type' => 'text_long',
    ]);
    $comment_body->save();

    FieldConfig::create([
      'field_storage' => $comment_body,
      'bundle' => 'comment_node_blog_entry',
      'label' => 'Comment',
    ])->save();
  }

  /**
   * Creates a blog entry comment.
   *
   * @param array $values
   *   The comment values.
   */
  protected function createBlogEntryComment(array $values = []): CommentInterface {
    $default_values = [
      'comment_type' => 'comment_node_blog_entry',
      'entity_type' => 'node',
      'field_name' => 'comment_node_blog_entry',
      'status' => NodeInterface::PUBLISHED,
      'comment_body' => [
        'value' => $this->randomString(),
        'format' => 'plain_text',
      ],
    ];

    return Comment::create($values + $default_values);
  }

}
