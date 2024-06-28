<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Parser\Html;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Parser\PlainTextParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a plain text parser.
 *
 * @covers \Drupal\external_content\Parser\PlainTextParser
 * @group external_content
 */
final class PlainTextParserTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testParser(): void {
    $child_parser = $this->prophesize(ChildHtmlParserInterface::class);
    $child_parser = $child_parser->reveal();

    $parser = new PlainTextParser();

    $document = new \DOMDocument();
    $generic_element = $document->createElement('div');
    $result = $parser->parseNode($generic_element, $child_parser);

    self::assertTrue($result->shouldContinue());
    self::assertFalse($result->shouldNotContinue());

    $empty_text = $document->createTextNode('');
    $result = $parser->parseNode($empty_text, $child_parser);

    self::assertTrue($result->shouldNotContinue());
    self::assertFalse($result->shouldContinue());

    $valid_text = $document->createTextNode('Hello, World!');
    $result = $parser->parseNode($valid_text, $child_parser);

    self::assertTrue($result->shouldNotContinue());
    self::assertFalse($result->shouldContinue());

    $replacement = $result->replacement();

    self::assertInstanceOf(PlainText::class, $replacement);
    self::assertEquals('Hello, World!', $replacement->getLiteral());
  }

}
