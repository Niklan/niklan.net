<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Converter;

use Drupal\external_content\Converter\ExternalContentMarkupConverter;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Converter\FooConverter;
use Drupal\external_content_test\Converter\FooReplacer;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides an external markup converter tests.
 *
 * @covers \Drupal\external_content\Converter\ExternalContentMarkupConverter
 * @group external_content
 */
final class ExternalContentMarkupConverterTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The class resolver.
   */
  protected EnvironmentAwareClassResolverInterface $classResolver;

  /**
   * The testing file.
   */
  protected ExternalContentFile $file;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->classResolver = $this
      ->container
      ->get(EnvironmentAwareClassResolverInterface::class);

    vfsStream::setup(structure: [
      'foo.md' => 'Hello, **foo**!',
    ]);

    $this->file = new ExternalContentFile(
      vfsStream::url('root'),
      vfsStream::url('root/foo.md'),
    );
  }

  /**
   * Tests that converter is properly called.
   */
  public function testConvert(): void {
    $environment = new Environment(new Configuration());
    $environment->addMarkupConverter(FooConverter::class);

    $converter = new ExternalContentMarkupConverter($this->classResolver);
    $converter->setEnvironment($environment);

    $result = $converter->convert($this->file);

    self::assertEquals('Hello, <strong>foo</strong>!', $result->getContent());
  }

  /**
   * Tests that pre-convert is called.
   */
  public function testPreConvert(): void {
    $environment = new Environment(new Configuration());
    $environment->addMarkupPreConverter(FooReplacer::class);
    $environment->addMarkupConverter(FooConverter::class);

    $converter = new ExternalContentMarkupConverter($this->classResolver);
    $converter->setEnvironment($environment);

    $result = $converter->convert($this->file);

    self::assertEquals('Hello, **bar**!', $result->getContent());
  }

  /**
   * Tests that post-convert is called.
   */
  public function testPostConvert(): void {
    $environment = new Environment(new Configuration());
    $environment->addMarkupConverter(FooConverter::class);
    $environment->addMarkupPostConverter(FooReplacer::class);

    $converter = new ExternalContentMarkupConverter($this->classResolver);
    $converter->setEnvironment($environment);

    $result = $converter->convert($this->file);

    self::assertEquals('Hello, <strong>bar</strong>!', $result->getContent());
  }

}
