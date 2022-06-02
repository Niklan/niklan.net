<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Converter;

use Drupal\external_content\Converter\ChainMarkupConverter;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Tests chained markup converter.
 *
 * @coversDefaultClass \Drupal\external_content\Converter\ChainMarkupConverter
 */
final class ChainMarkupConverterTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The chain markup converter.
   */
  protected ?ChainMarkupConverter $chainMarkupConverter;

  /**
   * Tests that converters works as expected.
   *
   * @dataProvider convertContent
   */
  public function testConverter(string $identifier, string $content, string $expected): void {
    $result = $this->chainMarkupConverter->convert($identifier, $content);
    self::assertEquals($expected, $result->value());
  }

  /**
   * Provides data for testing.
   *
   * @return array
   *   An array with values for testing.
   */
  public function convertContent(): array {
    return [
      'foo' => [
        'identifier' => 'foo',
        'content' => 'fo-foo-foo-fo',
        'expected' => 'fo-bar-bar-fo',
      ],
      'bar' => [
        'identifier' => 'bar',
        'content' => 'ba-bar-bar-ba',
        'expected' => 'ba-baz-baz-ba',
      ],
      // Make sure we always loop for all plugins.
      'foo again' => [
        'identifier' => 'foo',
        'content' => 'foo-bar-baz',
        'expected' => 'bar-bar-baz',
      ],
      // If identifier has no plugin, return raw value.
      'missing' => [
        'identifier' => 'non-existed',
        'content' => 'Hello, world!',
        'expected' => 'Hello, world!',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->chainMarkupConverter = $this->container->get(ChainMarkupConverter::class);
  }

}
