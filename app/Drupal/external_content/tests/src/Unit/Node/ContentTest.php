<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Node;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Content;
use Drupal\external_content_test\Node\SimpleNode;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content document test.
 *
 * @covers \Drupal\external_content\Node\Content
 * @group external_content
 */
final class ContentTest extends UnitTestCase {

  public function testObject(): void {
    $instance = new Content();

    self::assertFalse($instance->hasParent());
    self::assertNull($instance->getParent());
    self::assertEquals($instance, $instance->getRoot());
    self::assertInstanceOf(Data::class, $instance->getData());

    $node = new SimpleNode();
    $instance = $instance->setParent($node);

    self::assertFalse($instance->hasParent());
    self::assertNotEquals($node, $instance->getParent());
    self::assertNull($instance->getParent());
  }

}
