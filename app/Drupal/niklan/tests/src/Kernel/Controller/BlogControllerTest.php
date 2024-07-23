<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Controller\BlogController;
use Drupal\node\NodeInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;

/**
 * Provides a test for a blog controller.
 *
 * @coversDefaultClass \Drupal\niklan\Controller\BlogController
 */
final class BlogControllerTest extends NiklanTestBase {

  use BlogEntryTrait;

  /**
   * The blog controller.
   */
  protected BlogController $blogController;

  /**
   * Tests that controller properly handles empty list.
   */
  public function testEmptyList(): void {
    $result = $this->blogController->list();
    $this->render($result);

    self::assertCount(1, $this->cssSelect('.blog-posts'));
    self::assertCount(0, $this->cssSelect('.blog-posts article'));
  }

  /**
   * Tests that controller returns results as expected.
   */
  public function testList(): void {
    $this->createBlogEntry([
      'title' => 'Unpublished node',
      'status' => NodeInterface::NOT_PUBLISHED,
    ])->save();

    $this->createBlogEntry(['title' => 'Published node'])->save();

    $result = $this->blogController->list();
    $this->render($result);

    self::assertCount(1, $this->cssSelect('.blog-posts'));
    self::assertCount(1, $this->cssSelect('.blog-posts article'));
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->setUpBlogEntry();

    $this->blogController = $this
      ->container
      ->get('class_resolver')
      ->getInstanceFromDefinition(BlogController::class);
  }

}
