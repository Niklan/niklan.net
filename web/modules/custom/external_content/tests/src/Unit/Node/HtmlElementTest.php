<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Node;

use Drupal\external_content\Data\Attributes;
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

}
