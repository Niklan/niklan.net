<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Node;

use Drupal\external_content_test\Node\SimpleNode;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for an abstract node implementation.
 *
 * @covers \Drupal\external_content\Node\Node
 * @group external_content
 */
final class NodeTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $instance_a = new SimpleNode();

    self::assertFalse($instance_a->hasChildren());
    self::assertEquals([], $instance_a->getChildren()->getArrayCopy());
    self::assertFalse($instance_a->hasParent());
    self::assertEquals($instance_a, $instance_a->getRoot());

    $instance_b = new SimpleNode();

    self::assertFalse($instance_b->hasChildren());
    self::assertEquals([], $instance_b->getChildren()->getArrayCopy());
    self::assertFalse($instance_b->hasParent());
    self::assertEquals($instance_b, $instance_b->getRoot());

    $instance_a->addChild($instance_b);

    self::assertFalse($instance_a->hasParent());
    self::assertTrue($instance_b->hasParent());

    self::assertTrue($instance_a->hasChildren());
    self::assertEquals(
      [$instance_b],
      $instance_a->getChildren()->getArrayCopy(),
    );

    self::assertFalse($instance_b->hasChildren());
    self::assertEquals($instance_a, $instance_b->getParent());
    self::assertEquals($instance_a, $instance_b->getRoot());

    $instance_c = new SimpleNode();
    $instance_a->replaceNode($instance_b, $instance_c);

    self::assertEquals(
      [$instance_c],
      $instance_a->getChildren()->getArrayCopy(),
    );

    // Restore back: A → B.
    $instance_a->replaceNode($instance_c, $instance_b);
    // A → B → C.
    $instance_b->addChild($instance_c);
    $instance_d = new SimpleNode();
    // A → B → D.
    $instance_a->replaceNode($instance_c, $instance_d);

    self::assertEquals($instance_a, $instance_d->getRoot());
    self::assertEquals($instance_b, $instance_d->getParent());
    self::assertEquals($instance_a, $instance_b->getParent());
  }

}
