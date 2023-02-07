<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\ExternalContent;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for external content DTO.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\ExternalContent
 */
final class ExternalContentTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $external_content = new ExternalContent('test');
    self::assertEquals('test', $external_content->id());
    self::assertFalse($external_content->hasTranslation('ru'));
    self::assertNull($external_content->getTranslation('ru'));

    $file = new ParsedSourceFile(
      new SourceFile('foo', 'foo'),
      new SourceFileParams([]),
      new SourceFileContent(),
    );

    $external_content->addTranslation('ru', $file);
    self::assertTrue($external_content->hasTranslation('ru'));
    self::assertSame($file, $external_content->getTranslation('ru'));
  }

}
