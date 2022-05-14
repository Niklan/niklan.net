<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileMetadata;
use Drupal\Tests\UnitTestCase;

/**
 * Validates that parsed source file value object works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\ParsedSourceFile
 */
final class ParsedSourceFileTest extends UnitTestCase {

  /**
   * Tests that class works as expected.
   */
  public function testClass(): void {
    $file = new SourceFile('/home', '/home/foo.txt');
    $metadata = new SourceFileMetadata(['foo' => 'bar']);
    $content = new SourceFileContent('foo bar');
    $parsed_source_file = new ParsedSourceFile($file, $metadata, $content);

    $this->assertSame($file, $parsed_source_file->getFile());
    $this->assertSame($metadata, $parsed_source_file->getMetadata());
    $this->assertSame($content, $parsed_source_file->getContent());
  }

}
