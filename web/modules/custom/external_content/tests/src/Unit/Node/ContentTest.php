<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Node;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;
use Drupal\external_content_test\Node\SimpleNode;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content document test.
 *
 * @covers \Drupal\external_content\Node\Content
 * @group external_content
 */
final class ContentTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $file = new File('foo', 'bar', 'html');
    $instance = new Content($file);

    self::assertEquals($file, $instance->getSource());
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
