<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Finder\ExternalContentFinderInterface;
use Drupal\external_content\Plugin\ExternalContent\Configuration\Configuration;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides test for external content finder.
 *
 * @coversDefaultClass \Drupal\external_content\Finder\ExternalContentFinder
 */
final class ExternalContentFinderTest extends ExternalContentTestBase {

  /**
   * The external content finder.
   */
  protected ?ExternalContentFinderInterface $externalContentFinder;

  /**
   * Tests that find works as expected.
   */
  public function testFind(): void {
    vfsStream::setup(structure: [
      'foo' => [
        'bar' => [
          'baz' => [
            'foo.en.md' => <<<'Markdown'
            ---
            id: foo
            language: en
            ---

            Hello, world!
            Markdown,
            'foo.ru.markdown' => <<<'Markdown'
            ---
            id: foo
            language: ru
            ---

            Hello, world!
            Markdown,
          ],
          'bar.md' => <<<'Markdown'
          ---
          id: bar
          language: en
          ---

          Hello, world!
          Markdown,
        ],
      ],
    ]);

    $configuration = new Configuration('test', [
      'working_dir' => vfsStream::url('root'),
    ]);

    $result = $this->externalContentFinder->find($configuration);
    self::assertInstanceOf(ExternalContentCollection::class, $result);
    self::assertCount(2, $result);
    self::assertTrue($result->has('foo'));
    self::assertTrue($result->has('bar'));

    $foo = $result->get('foo');
    self::assertTrue($foo->hasTranslation('en'));
    self::assertTrue($foo->hasTranslation('ru'));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->externalContentFinder = $this->container->get(
        ExternalContentFinderInterface::class,
    );
  }

}
