<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\ElementBase;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for source file content.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\SourceFileContent
 */
final class SourceFileContentTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $element_a = new class() extends ElementBase {};
    $element_b = new class() extends ElementBase {};
    $element_a->addChild($element_b);
    $element_c = new class() extends ElementBase {};

    $content = new SourceFileContent();
    $content->addChild($element_a);
    $content->addChild($element_c);

    self::assertEquals(2, $content->getChildren()->count());
    self::assertSame($element_a, $content->getChildren()->offsetGet(0));
    self::assertSame($element_b, $content->getChildren()->offsetGet(0)->getChildren()->offsetGet(0));
    self::assertSame($element_c, $content->getChildren()->offsetGet(1));

    $element_d = new class() extends ElementBase {};
    $content->replaceElement($element_b, $element_d);

    self::assertEquals(2, $content->getChildren()->count());
    self::assertSame($element_a, $content->getChildren()->offsetGet(0));
    self::assertSame($element_d, $content->getChildren()->offsetGet(0)->getChildren()->offsetGet(0));
    self::assertSame($element_c, $content->getChildren()->offsetGet(1));

    self::assertSame($content, $element_d->getRoot());
  }

}
