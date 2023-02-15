<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Utility;

use Drupal\niklan\Utility\Anchor;
use Drupal\Tests\UnitTestCase;

/**
 * Tests text utility object.
 *
 * @coversDefaultClass \Drupal\niklan\Utility\Anchor
 */
final class AnchorTest extends UnitTestCase {

  /**
   * Tests anchor generation.
   *
   * @param string $text
   *   A text to process.
   * @param int $duplicate_mode
   *   A duplication mode.
   * @param string $expected
   *   An expected result.
   *
   * @dataProvider anchorProvider
   */
  public function testGenerator(string $text, int $duplicate_mode, string $expected): void {
    $actual = Anchor::generate($text, $duplicate_mode);
    $this->assertSame($expected, $actual);
  }

  /**
   * The anchor text values provider.
   *
   * @return array
   *   The array with data for testing.
   */
  public function anchorProvider(): array {
    return [
      'reusable for "test"' => ['test', Anchor::REUSE, 'test'],
      'reusable for "test" 1' => ['test', Anchor::REUSE, 'test'],
      'reusable for "test2"' => ['test2', Anchor::REUSE, 'test2'],
      'counter for "test"' => ['test', Anchor::COUNTER, 'test'],
      'counter for "test" 1' => ['test', Anchor::COUNTER, 'test-1'],
      'counter for "test2"' => ['test2', Anchor::COUNTER, 'test2'],
    ];
  }

}
