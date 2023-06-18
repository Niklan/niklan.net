<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Converter;

use Drupal\external_content\Contract\MarkupConverterInterface;
use Drupal\external_content\Contract\MarkupConverterPostprocessorInterface;
use Drupal\external_content\Contract\MarkupConverterPreprocessorInterface;
use Drupal\external_content\Converter\ExternalContentMarkupConverter;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\Environment\Environment;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;


final class ExternalContentMarkupConverterTest extends UnitTestCase {

  /**
   *
   */
  public function testConvert(): void {
    vfsStream::setup(structure: [
      'foo.md' => 'Hello, **foo**!',
    ]);

    $environment = new Environment(new Configuration());
    $environment->addMarkupConverterPreprocessor(new FooPreprocessor());
    $environment->addMarkupConverter(new FakeMarkupConverter());
    $environment->addMarkupConverterPostprocessor(new BarPostprocessor());

    $file = new ExternalContentFile(
      vfsStream::url('root'),
      vfsStream::url('root/foo.md'),
    );

    $converter = new ExternalContentMarkupConverter($environment);
    $result = $converter->convert($file);

    self::assertTrue($result->hasData('foo'));
    self::assertEquals('preprocessor', $result->getData('foo'));

    self::assertTrue($result->hasData('bar'));
    self::assertEquals('postprocessor', $result->getData('bar'));

    self::assertEquals('Hello, <strong>foo</strong>!', $result->getContent());
  }

}














final class FakeMarkupConverter implements MarkupConverterInterface {

  /**
   *
   */
  public function convert(ExternalContentHtml $result): ExternalContentHtml {
    $content = $result->getContent();
    $content = \str_replace('**foo**', '<strong>foo</strong>', $content);
    $result->setContent($content);

    return $result;
  }

}














final class FooPreprocessor implements MarkupConverterPreprocessorInterface {

  /**
   *
   */
  public function preprocess(ExternalContentHtml $result): ExternalContentHtml {
    $result->addData('foo', 'preprocessor');

    return $result;
  }

}














final class BarPostprocessor implements MarkupConverterPostprocessorInterface {

  /**
   *
   */
  public function postprocess(ExternalContentHtml $result): ExternalContentHtml {
    $result->addData('bar', 'postprocessor');

    return $result;
  }

}
