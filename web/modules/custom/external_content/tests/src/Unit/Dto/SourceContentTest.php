<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\ElementBase;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\Tests\UnitTestCase;

/**
 * Validates that source content value object works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\SourceFileContent
 */
final class SourceContentTest extends UnitTestCase {

  /**
   * Tests that class works as expected.
   */
  public function testClass(): void {
    $element = new class() extends ElementBase {};
    $source_content = new SourceFileContent();
    $this->assertEquals(0, $source_content->getElements()->count());
    $source_content->addElement($element);
    $this->assertEquals(1, $source_content->getElements()->count());
    $this->assertSame($element, $source_content->getElements()->offsetGet(0));
  }

}
