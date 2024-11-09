<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Comment\Controller\CommentList;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryCommentTrait;

/**
 * Provides a test for comment controller.
 *
 * @coversDefaultClass \Drupal\niklan\Comment\Controller\CommentList
 */
final class CommentControllerTest extends NiklanTestBase {

  use BlogEntryCommentTrait;

  /**
   * Tests a comments controller with empty results.
   */
  public function testEmptyList(): void {
    $controller = CommentList::create($this->container);
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

    $controller = CommentList::create($this->container);
    $result = $controller->list();
    $this->render($result);

    self::assertCount(1, $this->cssSelect('.comments-list'));
    self::assertCount(1, $this->cssSelect('.comments-list__items'));
    self::assertCount(1, $this->cssSelect('.comments-list article'));
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntryComment();
  }

}
