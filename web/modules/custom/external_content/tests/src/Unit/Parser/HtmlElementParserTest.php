<?php declare(strict_types = 1);

namespace Drupal\comment\Tests\external_content\Unit\Parser;

use Drupal\external_content\Data\HtmlParserResultReplace;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a HTML element parser test.
 *
 * @covers \Drupal\external_content\Parser\Html\ElementParser
 * @group external_content
 */
final class HtmlElementParserTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testParse(): void {
    $document = new \DOMDocument();
    $node = $document->createElement('div');
    $node->setAttribute('data-foo', 'bar');

    $parser = new ElementParser();
    $result = $parser->parseNode($node);

    self::assertInstanceOf(HtmlParserResultReplace::class, $result);

    $replacement = $result->getReplacement();

    self::assertInstanceOf(HtmlElement::class, $replacement);
    self::assertEquals('div', $replacement->getTag());
    self::assertEquals(
      'bar',
      $replacement->getAttributes()->getAttribute('data-foo'),
    );
  }

}
