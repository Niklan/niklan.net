<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Parser;

use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Parser\SourceFileParser;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Validates that parser extract information properly.
 *
 * @coversDefaultClass \Drupal\external_content\Parser\SourceFileParser
 */
final class SourceFileParserTest extends UnitTestCase {

  /**
   * Tests parser functionality.
   */
  public function testParser(): void {
    $file_contents = <<<'Markdown'
    ---
    slug: foo
    title: Hello, world!
    metatags:
      title: Hello!
    ---
    The content!
    Markdown;

    vfsStream::setup(structure: [
      'foo.md' => $file_contents,
    ]);

    $file = new SourceFile(
      vfsStream::url('root'),
      vfsStream::url('root/foo.md'),
    );

    $parser = new SourceFileParser();
    $parsed_file = $parser->parse($file);

    $this->assertSame($file, $parsed_file->getFile());
    $expected_params = [
      'slug' => 'foo',
      'title' => 'Hello, world!',
      'metatags' => [
        'title' => 'Hello!',
      ],
    ];
    $this->assertEquals($expected_params, $parsed_file->getParams()->all());
    $this->assertEquals('The content!', $parsed_file->getContent()->getContent());
  }

}
