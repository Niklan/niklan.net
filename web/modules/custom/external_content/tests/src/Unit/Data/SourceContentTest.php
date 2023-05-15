<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Element;
use Drupal\external_content\Data\SourceFileContent;
use Drupal\Tests\UnitTestCase;

/**
 * Validates that source content value object works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Data\SourceFileContent
 */
final class SourceContentTest extends UnitTestCase {

  /**
   * Tests that class works as expected.
   */
  public function testClass(): void {
    $element = new class() extends Element {};
    $source_content = new SourceFileContent();
    $this->assertEquals(0, $source_content->getChildren()->count());
    $source_content->addChild($element);
    $this->assertEquals(1, $source_content->getChildren()->count());
    $this->assertSame($element, $source_content->getChildren()->offsetGet(0));
  }

}
