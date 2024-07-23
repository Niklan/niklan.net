<?php

declare(strict_types=1);

namespace Drupal\comment\Tests\external_content\Unit\Parser\Html;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\NodeList;
use Drupal\external_content\Parser\ElementParser;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

/**
 * Provides a HTML element parser test.
 *
 * @covers \Drupal\external_content\Parser\ElementParser
 * @group external_content
 */
final class HtmlElementParserTest extends UnitTestCase {

  public function testParse(): void {
    $child = new Element('foo');
    $children = new NodeList();
    $children->addChild($child);

    $child_parser = $this->prophesize(ChildHtmlParserInterface::class);
    $child_parser
      ->parse(Argument::any())
      ->shouldBeCalledOnce()
      ->willReturn($children);
    $child_parser = $child_parser->reveal();

    $document = new \DOMDocument();
    $node = $document->createElement('div');
    $node->setAttribute('data-foo', 'bar');

    $parser = new ElementParser();
    $result = $parser->parseNode($node, $child_parser);

    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertTrue($result->hasReplacement());
    self::assertFalse($result->hasNoReplacement());

    $replacement = $result->replacement();

    self::assertInstanceOf(Element::class, $replacement);
    self::assertEquals('div', $replacement->getTag());
    self::assertEquals(
      'bar',
      $replacement->getAttributes()->getAttribute('data-foo'),
    );
    self::assertTrue($replacement->hasChildren());
    self::assertSame([$child], $replacement->getChildren()->getArrayCopy());
  }

}
