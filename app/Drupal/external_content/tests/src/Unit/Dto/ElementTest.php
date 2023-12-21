<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content_test\Dto\FooBarElement;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for base element.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\Element
 */
final class ElementTest extends UnitTestCase {

  /**
   * Tests that element works as expected.
   */
  public function testElement(): void {
    $element = new FooBarElement();

    self::assertFalse($element->hasParent());
    self::assertNull($element->getParent());
    self::assertEquals(0, $element->getChildren()->count());
    self::assertFalse($element->hasChildren());
    self::assertSame($element, $element->getRoot());

    $child = new FooBarElement();
    // - $element
    // -- $child
    $element->addChild($child);

    self::assertTrue($child->hasParent());
    self::assertSame($element, $child->getParent());
    self::assertSame($element, $child->getRoot());
    self::assertEquals(1, $element->getChildren()->count());

    $replacement = new FooBarElement();
    // - $element
    // -- $replacement
    $element->replaceElement($child, $replacement);

    self::assertNotSame($child, $replacement);
    self::assertSame($replacement, $element->getChildren()->offsetGet(0));

    $child_depth_2 = new FooBarElement();
    // - $element
    // -- $replacement
    // --- $child_depth_2
    $replacement->addChild($child_depth_2);

    $replacement_2 = new FooBarElement();
    // - $element
    // -- $replacement
    // --- $replacement_2
    $element->replaceElement($child_depth_2, $replacement_2);

    $final_child_1 = $element->getChildren()->offsetGet(0);
    \assert($final_child_1 instanceof FooBarElement);
    self::assertSame($final_child_1, $replacement);
    $final_child_2 = $final_child_1->getChildren()->offsetGet(0);
    self::assertSame($replacement_2, $final_child_2);
  }

}
