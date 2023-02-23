<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Element;

use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;

/**
 * Provides a test for 'niklan_last_blog_posts' render element.
 *
 * @coversDefaultClass \Drupal\niklan\Element\LastBlogPosts
 */
final class LastBlogPostsTest extends NiklanTestBase {

  use BlogEntryTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
  }

  /**
   * Tests that element properly works with no blog posts.
   */
  public function testWithNoBlogPosts(): void {
    $element = [
      '#type' => 'niklan_last_blog_posts',
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.last-content'));
  }

  /**
   * Tests that element properly works with blog posts.
   */
  public function testWithBlogPosts(): void {
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();

    $element = [
      '#type' => 'niklan_last_blog_posts',
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.last-content'));
    self::assertCount(3, $this->cssSelect('.last-content article'));
  }

  /**
   * Tests that limitation works as expected.
   */
  public function testLimitProperty(): void {
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();
    $this->createBlogEntry()->save();

    $element = [
      '#type' => 'niklan_last_blog_posts',
      '#limit' => 5,
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.last-content'));
    self::assertCount(5, $this->cssSelect('.last-content article'));
  }

}
