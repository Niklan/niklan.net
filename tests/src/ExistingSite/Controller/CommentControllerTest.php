<?php

declare(strict_types=1);

namespace Niklan\Tests\ExistingSite\Controller;

use Drupal\comment\Entity\Comment;
use Drupal\Component\Utility\Random;
use Drupal\Core\Render\RendererInterface;
use Drupal\niklan\Controller\CommentController;
use Drupal\node\Entity\Node;
use Niklan\Tests\Traits\EntityCleanupTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for comment controller test.
 *
 * @coversDefaultClass \Drupal\niklan\Controller\CommentController
 */
final class CommentControllerTest extends ExistingSiteBase {

  use EntityCleanupTrait;

  /**
   * The renderer.
   */
  protected RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds(['node', 'comment']);
    $this->renderer = $this->container->get('renderer');
  }

  /**
   * Tests that list controller works as expected.
   */
  public function testList(): void {
    $random = new Random();
    $comment_body = $random->word(10);

    $controller = CommentController::create($this->container);
    $result = $controller->list();
    $result_html = $this->renderer->renderPlain($result);
    $this->assertStringNotContainsString($comment_body, (string) $result_html);

    $blog_post = Node::create([
      'type' => 'blog_entry',
      'title' => 'Hello, world!',
    ]);
    $blog_post->save();

    $comment = Comment::create([
      'comment_type' => 'comment_node_blog_entry',
      'entity_type' => 'node',
      'entity_id' => $blog_post->id(),
      'field_name' => 'comment_node_blog_entry',
      'title' => $blog_post->label(),
      'comment_body' => [
        'value' => $comment_body,
      ],
    ]);
    $comment->save();

    $result = $controller->list();
    $result_html = $this->renderer->renderPlain($result);
    $this->assertStringContainsString($comment_body, (string) $result_html);
  }

}
