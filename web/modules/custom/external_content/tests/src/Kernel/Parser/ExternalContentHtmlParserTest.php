<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Parser;

use Drupal\external_content\Contract\Parser\ExternalContentHtmlParserInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Event\HtmlPostParseEvent;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides a test for external content HTML parser.
 *
 * @group external_content
 * @covers \Drupal\external_content\Parser\ExternalContentHtmlParser
 */
final class ExternalContentHtmlParserTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  protected ExternalContentHtmlParserInterface $htmlParser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->htmlParser = $this
      ->container
      ->get(ExternalContentHtmlParserInterface::class);

    vfsStream::setup(structure: [
      'foo' => [
        'bar.html' => '<p>Hello, World!</p>',
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

    $environment = new Environment(new Configuration());
    $environment->addEventListener(
      HtmlPreParseEvent::class,
      $pre_parse_listener,
    );
    $environment->addEventListener(
      HtmlPostParseEvent::class,
      $post_parse_listener,
    );

    $file = new ExternalContentFile(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
    );
    $this->htmlParser->setEnvironment($environment);

    // Make sure they are FALSE before parsing is called.
    self::assertFalse($is_pre_parse_triggered);
    self::assertFalse($is_post_parse_triggered);

    $this->htmlParser->parse($file);

    self::assertTrue($is_pre_parse_triggered);
    self::assertTrue($is_post_parse_triggered);
  }

}
