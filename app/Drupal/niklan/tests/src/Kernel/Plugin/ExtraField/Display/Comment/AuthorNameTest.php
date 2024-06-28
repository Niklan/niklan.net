<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\ExtraField\Display\Comment;

use Drupal\comment\CommentInterface;
use Drupal\comment\Entity\Comment;
use Drupal\comment\Entity\CommentType;
use Drupal\Tests\niklan\Kernel\Plugin\ExtraField\ExtraFieldTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\user\UserInterface;

/**
 * Provides a test for extra field with author name.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\ExtraField\Display\Comment\AuthorName
 */
final class AuthorNameTest extends ExtraFieldTestBase {

  use UserCreationTrait;

  /**
   * The created comment author entity.
   */
  protected ?UserInterface $author;

  /**
   * The created comment entity.
   */
  protected ?CommentInterface $comment;

  /**
   * Tests that extra field results contains expected value.
   */
  public function testView(): void {
    $plugin = $this->createExtraFieldDisplayInstance('author_name');
    $build = $plugin->view($this->comment);
    $this->render($build);

    self::assertRaw($this->author->getAccountName());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('comment');

    $this->author = $this->createUser();

    CommentType::create([
      'id' => 'test',
    ]);

    $this->comment = Comment::create([
      'comment_type' => 'test',
      'uid' => $this->author->id(),
    ]);
  }

}
