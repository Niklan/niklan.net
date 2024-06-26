<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Controller\CommentController;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryCommentTrait;

/**
 * Provides a test for comment controller.
 *
 * @coversDefaultClass \Drupal\niklan\Controller\CommentController
 */
final class CommentControllerTest extends NiklanTestBase {

  use BlogEntryCommentTrait;

  /**
   * Tests a comments controller with empty results.
   */
  public function testEmptyList(): void {
    $controller = CommentController::create($this->container);
    $result = $controller->list();
    $this->render($result);

    self::assertCount(1, $this->cssSelect('.comments-list'));
    self::assertCount(0, $this->cssSelect('.comments-list__items'));
    self::assertCount(0, $this->cssSelect('.comments-list article'));
  }

  /**
   * Tests a comments controller with results.
   */
  public function testList(): void {
    $this->createBlogEntryComment()->save();

    $controller = CommentController::create($this->container);
    $result = $controller->list();
    $this->render($result);

    self::assertCount(1, $this->cssSelect('.comments-list'));
    self::assertCount(1, $this->cssSelect('.comments-list__items'));
    self::assertCount(1, $this->cssSelect('.comments-list article'));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntryComment();
  }

}
