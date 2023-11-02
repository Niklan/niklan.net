<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Parser;

use Drupal\external_content\Contract\Parser\HtmlParserFacadeInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Event\HtmlPostParseEvent;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Node\Html\PlainText;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides a test for external content HTML parser.
 *
 * @group external_content
 * @covers \Drupal\external_content\Parser\Html\HtmlParser
 */
final class ExternalContentHtmlParserTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  protected HtmlParserFacadeInterface $htmlParser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->htmlParser = $this
      ->container
      ->get(HtmlParserFacadeInterface::class);

    vfsStream::setup(structure: [
      'foo' => [
        'bar.html' => '<p>Hello, <strong>World</strong>!</p>',
      ],
    ]);
  }

  /**
   * {@selfdoc}
   */
  public function testEvents(): void {
    $is_pre_parse_triggered = FALSE;
    $is_post_parse_triggered = FALSE;

    $pre_parse_listener = static function () use (&$is_pre_parse_triggered): void {
      $is_pre_parse_triggered = TRUE;
    };
    $post_parse_listener = static function () use (&$is_post_parse_triggered): void {
      $is_post_parse_triggered = TRUE;
    };

    $environment = new Environment();
    $environment->addEventListener(
      HtmlPreParseEvent::class,
      $pre_parse_listener,
    );
    $environment->addEventListener(
      HtmlPostParseEvent::class,
      $post_parse_listener,
    );

    $file = new File(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
      'html',
    );
    $this->htmlParser->setEnvironment($environment);

    // Make sure they are FALSE before parsing is called.
    self::assertFalse($is_pre_parse_triggered);
    self::assertFalse($is_post_parse_triggered);

    $this->htmlParser->parse($file);

    self::assertTrue($is_pre_parse_triggered);
    self::assertTrue($is_post_parse_triggered);
  }

  /**
   * {@selfdoc}
   */
  public function testParse(): void {
    $file = new File(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
      'html',
    );

    $environment = new Environment();
    $environment->addParser(new PlainTextParser());
    $environment->addParser(new ElementParser());
    $this->htmlParser->setEnvironment($environment);

    $result = $this->htmlParser->parse($file);

    $p = new Element('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new Element('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));

    $expected_result = new Content($file);
    $expected_result->addChild($p);

    self::assertEquals($expected_result, $result);
  }

  /**
   * {@selfdoc}
   */
  public function testWithoutParsers(): void {
    $file = new File(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
      'html',
    );

    $environment = new Environment();
    $this->htmlParser->setEnvironment($environment);

    $result = $this->htmlParser->parse($file);

    $expected_result = new Content($file);

    self::assertEquals($expected_result, $result);
  }

}
