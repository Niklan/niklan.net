<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Parser\Html;

use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Node\Html\PlainText;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides a test for external content HTML parser.
 *
 * @group external_content
 * @covers \Drupal\external_content\Parser\Html\HtmlParser
 */
final class HtmlParserTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  private function getParser(): ParserInterface {
    return $this->container->get(ParserInterface::class);
  }

  /**
   * {@selfdoc}
   */
  private function prepareSourceDir(): void {
    vfsStream::setup(structure: [
      'foo' => [
        'bar.html' => '<p>Hello, <strong>World</strong>!</p>',
      ],
    ]);
  }

  /**
   * {@selfdoc}
   */
  public function testParse(): void {
    $this->prepareSourceDir();

    $file = new File(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
      'html',
    );

    $environment = new Environment();
    $environment->addExtension(new BasicHtmlExtension());
    $this->getParser()->setEnvironment($environment);

    $result = $this->getParser()->parse($file);

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
    $this->prepareSourceDir();

    $file = new File(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
      'html',
    );

    $environment = new Environment();
    $environment->addParser(new HtmlParser());
    $this->getParser()->setEnvironment($environment);

    self::expectExceptionMessage('Missing config schema for "html"');
    $this->getParser()->parse($file);
  }

}
