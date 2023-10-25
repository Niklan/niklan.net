<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Parser;

use Drupal\external_content\Data\HtmlParserResultContinue;
use Drupal\external_content\Data\HtmlParserResultFinalize;
use Drupal\external_content\Data\HtmlParserResultStop;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a plain text parser.
 *
 * @covers \Drupal\external_content\Parser\Html\PlainTextParser
 * @group external_content
 */
final class PlainTextParserTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testParser(): void {
    $parser = new PlainTextParser();

    $document = new \DOMDocument();
    $generic_element = $document->createElement('div');
    $result = $parser->parse($generic_element);

    self::assertInstanceOf(HtmlParserResultContinue::class, $result);

    $empty_text = $document->createTextNode('');
    $result = $parser->parse($empty_text);

    self::assertInstanceOf(HtmlParserResultStop::class, $result);

    $valid_text = $document->createTextNode('Hello, World!');
    $result = $parser->parse($valid_text);

    self::assertInstanceOf(HtmlParserResultFinalize::class, $result);

    $replacement = $result->getReplacement();

    self::assertInstanceOf(PlainText::class, $replacement);
    self::assertEquals('Hello, World!', $replacement->getContent());
  }

}
