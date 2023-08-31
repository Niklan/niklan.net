<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Node;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\HtmlElement;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a HTML element test.
 *
 * @covers \Drupal\external_content\Node\HtmlElement
 * @group external_content
 */
final class HtmlElementTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $attributes = new Attributes();
    $attributes->setAttribute('foo', 'bar');
    $instance = new HtmlElement('div', $attributes);

    self::assertEquals('div', $instance->getTag());
    self::assertSame($attributes, $instance->getAttributes());
  }

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $attributes = new Attributes();
    $attributes->setAttribute('foo', 'bar');
    $instance = new HtmlElement('div', $attributes);

    $expected_data = new Data([
      'tag' => 'div',
      'attributes' => [
        'foo' => 'bar',
      ],
    ]);

    self::assertEquals($expected_data, $instance->serialize());

    $instance_from_data = HtmlElement::deserialize($expected_data);

    self::assertEquals($instance, $instance_from_data);
  }

}
