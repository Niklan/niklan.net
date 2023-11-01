<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Parser;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Parser\Html\HtmlParser
 * @group external_content
 */
final class HtmlParserTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testParser(): void {
    $environment = new Environment();
    $environment->addExtension(new BasicHtmlExtension());

    $html = $this->prophesize(SourceInterface::class);
    $html->contents()->willReturn('<p>Hello, World!</p>');
    $html = $html->reveal();

    $html_parser = new HtmlParser();
    $html_parser->setEnvironment($environment);
    $result = $html_parser->parse($html);

    $p = $result->getChildren()->offsetGet(0);
    self::assertInstanceOf(HtmlElement::class, $p);
    self::assertSame('p', $p->getTag());

    $text = $p->getChildren()->offsetGet(0);
    self::assertInstanceOf(PlainText::class, $text);
    self::assertSame('Hello, World!', $text->getContent());
  }

}
