<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

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
    $source_content = new SourceFileContent('foo bar');
    $this->assertEquals('foo bar', $source_content->value());
  }

}
