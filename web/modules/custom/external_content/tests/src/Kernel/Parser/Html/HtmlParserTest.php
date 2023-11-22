<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Parser\Html;

use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Data\PrioritizedList;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Node\Html\PlainText;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use League\Config\Configuration;
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

    $html_parsers = new PrioritizedList();
    $html_parsers->add(new ElementParser(), 0);
    $html_parsers->add(new PlainTextParser(), 1000);

    $configuration = new Configuration();
    $configuration->set('html.parsers', $html_parsers);

    $environment = new Environment($configuration);
    $environment->addExtension(new BasicHtmlExtension());
    $this->getParser()->setEnvironment($environment);

    $result = $this->getParser()->parse($file);

    $p = new Element('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new Element('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));

    $data = new Data();
    $data->set('source', [
      'id' => 'foo/bar.html',
      'type' => 'html',
      'data' => [],
    ]);
    $expected_result = new Content($data);
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

  /**
   * {@selfdoc}
   */
  public function testWithoutSuitableParser(): void {
    $this->prepareSourceDir();

    $file = new File(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar.html'),
      'html',
    );

    $configuration = new Configuration();
    $configuration->set('html.parsers', new PrioritizedList());

    $environment = new Environment($configuration);
    $environment->addExtension(new BasicHtmlExtension());
    $this->getParser()->setEnvironment($environment);

    $result = $this->getParser()->parse($file);
    self::assertCount(0, $result->getChildren());
  }

}
