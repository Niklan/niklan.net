<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Controller\TagController;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;
use Drupal\Tests\niklan\Traits\TagsTrait;

/**
 * Provides a tag controller test.
 *
 * @coversDefaultClass \Drupal\niklan\Controller\TagController
 */
final class TagControllerTest extends NiklanTestBase {

  use BlogEntryTrait;
  use TagsTrait;

  /**
   * Tests an empty collection result.
   */
  public function testEmptyCollection(): void {
    $controller = TagController::create($this->container);
    $result = $controller->collection();

    $this->render($result);

    self::assertCount(1, $this->cssSelect('.tag-list'));
    self::assertCount(0, $this->cssSelect('.tag-list h2'));
  }

  /**
   * Tests a collection result.
   */
  public function testCollection(): void {
    $tag_a = $this->createTag(['name' => 'Tag A']);
    $tag_a->save();

    $article_a = $this->createBlogEntry(['title' => 'Blog A']);
    $article_a->set('field_tags', [$tag_a]);
    $article_a->save();

    $controller = TagController::create($this->container);
    $result = $controller->collection();

    $this->render($result);

    self::assertCount(1, $this->cssSelect('.tag-list'));
    self::assertCount(1, $this->cssSelect('.tag-list h2'));
    self::assertRaw('Tag A');
  }

  /**
   * Tests an empty page result.
   */
  public function testEmptyPage(): void {
    $tag_a = $this->createTag(['name' => 'Tag A']);
    $tag_a->save();

    $controller = TagController::create($this->container);
    $result = $controller->page($tag_a);

    $this->render($result);

    self::assertCount(1, $this->cssSelect('.tag-page'));
    self::assertCount(0, $this->cssSelect('.tag-page__items'));
    self::assertCount(0, $this->cssSelect('.tag-page article'));
  }

  /**
   * Tests a page result.
   */
  public function testPage(): void {
    $tag_a = $this->createTag(['name' => 'Tag A']);
    $tag_a->save();

    $blog_a = $this->createBlogEntry(['title' => 'Blog A']);
    $blog_a->set('field_tags', [$tag_a]);
    $blog_a->save();

    $controller = TagController::create($this->container);
    $result = $controller->page($tag_a);

    $this->render($result);

    self::assertCount(1, $this->cssSelect('.tag-page'));
    self::assertCount(1, $this->cssSelect('.tag-page__items'));
    self::assertCount(1, $this->cssSelect('.tag-page article'));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
    $this->setUpTagsVocabulary();
  }

}
