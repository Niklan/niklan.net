<?php declare(strict_types = 1);

namespace Drupal\comment\Tests\external_content\Unit\Parser\Html;

use Drupal\external_content\Data\HtmlParserResultReplace;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Parser\ElementParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a HTML element parser test.
 *
 * @covers \Drupal\external_content\Parser\ElementParser
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

    self::assertInstanceOf(Element::class, $replacement);
    self::assertEquals('div', $replacement->getTag());
    self::assertEquals(
      'bar',
      $replacement->getAttributes()->getAttribute('data-foo'),
    );
  }

}
