<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Element;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;

/**
 * Provides a test for 'niklan_previous_next' render element.
 *
 * @coversDefaultClass \Drupal\niklan\Element\PreviousNext
 */
final class PreviousNextTest extends NiklanTestBase {

  use BlogEntryTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
    $this->installEntitySchema('entity_test');
  }

  /**
   * Tests that element behaves properly when no entity provided.
   */
  public function testWithNoEntityProvided(): void {
    $element = [
      '#type' => 'niklan_previous_next',
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.previous-next'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--previous'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--next'));
  }

  /**
   * Tests that element behaves properly when only a single entity exists.
   */
  public function testWithSingleEntity(): void {
    $blog_a = $this->createBlogEntry();
    $blog_a->save();

    $element = [
      '#type' => 'niklan_previous_next',
      '#entity' => $blog_a,
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.previous-next'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--previous'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--next'));
  }

  /**
   * Tests that element behaves properly when only next link is found.
   */
  public function testWithNextOnly(): void {
    $blog_a = $this->createBlogEntry(['created' => 1]);
    $blog_a->save();
    $blog_b = $this->createBlogEntry(['created' => 2]);
    $blog_b->save();

    $element = [
      '#type' => 'niklan_previous_next',
      '#entity' => $blog_b,
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.previous-next'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--previous'));
    self::assertCount(1, $this->cssSelect('.previous-next__link--next'));
  }

  /**
   * Tests that element behaves properly when only previous link is found.
   */
  public function testWithPreviousOnly(): void {
    $blog_a = $this->createBlogEntry(['created' => 1]);
    $blog_a->save();
    $blog_b = $this->createBlogEntry(['created' => 2]);
    $blog_b->save();

    $element = [
      '#type' => 'niklan_previous_next',
      '#entity' => $blog_a,
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.previous-next'));
    self::assertCount(1, $this->cssSelect('.previous-next__link--previous'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--next'));
  }

  /**
   * Tests that element behaves properly when both links are found.
   */
  public function testWithBothLinks(): void {
    $blog_a = $this->createBlogEntry(['created' => 1]);
    $blog_a->save();
    $blog_b = $this->createBlogEntry(['created' => 2]);
    $blog_b->save();
    $blog_c = $this->createBlogEntry(['created' => 3]);
    $blog_c->save();
    $blog_d = $this->createBlogEntry(['created' => 4]);
    $blog_d->save();
    $blog_e = $this->createBlogEntry(['created' => 5]);
    $blog_e->save();

    $element = [
      '#type' => 'niklan_previous_next',
      '#entity' => $blog_c,
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.previous-next'));
    self::assertCount(1, $this->cssSelect('.previous-next__link--previous'));
    self::assertCount(1, $this->cssSelect('.previous-next__link--next'));
    self::assertRaw('/node/2');
    self::assertRaw('/node/4');
  }

  /**
   * Tests that element properly handles entities without ::getCreatedTime().
   */
  public function testEntityWithoutGetCreatedTime(): void {
    $entity = EntityTest::create();
    $entity->save();

    $element = [
      '#type' => 'niklan_previous_next',
      '#entity' => $entity,
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.previous-next'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--previous'));
    self::assertCount(0, $this->cssSelect('.previous-next__link--next'));
  }

}
