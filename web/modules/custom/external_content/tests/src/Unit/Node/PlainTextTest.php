<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Node;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for plain text element.
 *
 * @covers \Drupal\external_content\Node\PlainText
 * @group external_content
 */
final class PlainTextTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $text = 'Hello, World!';
    $instance = new PlainText($text);

    self::assertEquals($text, $instance->getContent());
  }

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $instance = new PlainText('Hello, World!');

    $expected_data = new Data([
      'text' => 'Hello, World!',
    ]);

    self::assertEquals($expected_data, $instance->serialize());

    $instance_from_data = PlainText::deserialize($expected_data);

    self::assertEquals($instance, $instance_from_data);
  }

}
