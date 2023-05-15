<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ParsedSourceFile;
use Drupal\external_content\Data\SourceFile;
use Drupal\external_content\Data\SourceFileContent;
use Drupal\external_content\Data\SourceFileParams;
use Drupal\Tests\UnitTestCase;

/**
 * Validates that parsed source file value object works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Data\ParsedSourceFile
 */
final class ParsedSourceFileTest extends UnitTestCase {

  /**
   * Tests that class works as expected.
   */
  public function testClass(): void {
    $file = new SourceFile('/home', '/home/foo.txt');
    $params = new SourceFileParams(['foo' => 'bar']);
    $content = new SourceFileContent();
    $parsed_source_file = new ParsedSourceFile($file, $params, $content);

    $this->assertSame($file, $parsed_source_file->getFile());
    $this->assertSame($params, $parsed_source_file->getParams());
    $this->assertSame($content, $parsed_source_file->getContent());
  }

}
