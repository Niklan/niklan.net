<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Node;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content_test\Node\SimpleNode;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content document test.
 *
 * @covers \Drupal\external_content\Node\ExternalContentDocument
 * @group external_content
 */
final class ExternalContentDocumentTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $file = new ExternalContentFile('foo', 'bar');
    $instance = new ExternalContentDocument($file);

    self::assertEquals($file, $instance->getFile());
    self::assertFalse($instance->hasParent());
    self::assertNull($instance->getParent());
    self::assertEquals($instance, $instance->getRoot());

    $node = new SimpleNode();
    $instance->setParent($node);

    self::assertFalse($instance->hasParent());
    self::assertNotEquals($node, $instance->getParent());
    self::assertNull($instance->getParent());
  }

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $file = new ExternalContentFile('foo', 'bar');
    $instance = new ExternalContentDocument($file);

    $expected_data = new Data([
      'file' => [
        'working_dir' => 'foo',
        'pathname' => 'bar',
        'data' => [],
      ],
    ]);

    self::assertEquals($expected_data, $instance->serialize());

    $instance_from_data = ExternalContentDocument::deserialize($expected_data);

    self::assertEquals($instance, $instance_from_data);
  }

}
