<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\HtmlElement;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for HTML element DTO.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\HtmlElement
 */
final class HtmlElementTest extends UnitTestCase {

  /**
   * Tests that works as expected.
   */
  public function testElement(): void {
    $element = new HtmlElement('div');
    self::assertEquals('div', $element->getTag());
    self::assertEmpty($element->getAttributes());
    self::assertFalse($element->hasAttribute('div'));
    self::assertNull($element->getAttribute('div'));
    $element->setAttribute('class', 'foo-bar');
    self::assertTrue($element->hasAttribute('class'));
    self::assertEquals(['class' => 'foo-bar'], $element->getAttributes());
    $element->setAttributes([]);
    self::assertEmpty($element->getAttributes());
    self::assertNull($element->getAttribute('class'));
    self::assertFalse($element->hasAttribute('class'));

    $element_2 = new HtmlElement('div', ['data-test' => 'foo-bar']);
    self::assertNotEmpty($element_2->getAttributes());
    self::assertEquals(['data-test' => 'foo-bar'], $element_2->getAttributes());
    self::assertEquals('foo-bar', $element_2->getAttribute('data-test'));
  }

}
