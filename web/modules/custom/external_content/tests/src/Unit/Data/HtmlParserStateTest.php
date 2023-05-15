<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Contract\ChainHtmlParserInterface;
use Drupal\external_content\Data\HtmlParserState;
use Drupal\external_content\Data\SourceFile;
use Drupal\external_content\Data\SourceFileParams;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides an HTML parser state.
 *
 * @coversDefaultClass \Drupal\external_content\Data\HtmlParserState
 */
final class HtmlParserStateTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $source_file = new SourceFile('', '');
    $source_file_params = new SourceFileParams([]);
    $parser = $this
      ->prophesize(ChainHtmlParserInterface::class)
      ->reveal();

    $parser_state = new HtmlParserState(
      $source_file,
      $source_file_params,
      $parser,
    );

    self::assertSame($parser, $parser_state->getParser());
    self::assertSame($source_file, $parser_state->getSourceFile());
    self::assertSame(
      $source_file_params,
      $parser_state->getSourceFileParams(),
    );
  }

}
