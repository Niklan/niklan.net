<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Hook\Theme;

use Drupal\niklan\Hook\Theme\LibraryInfoAlter;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LibraryInfoAlter::class)]
final class LibraryInfoAlterTest extends NiklanTestBase {

  protected LibraryInfoAlter $libraryInfoAlter;

  /**
   * Tests that 'drupal.ajax' not altered for other extensions.
   */
  public function testAlterDrupalAjaxWithMissingLibrary(): void {
    $libraries = [
      'drupal.ajax' => [
        'js' => [],
      ],
    ];
    $expected_libraries = $libraries;

    $this->libraryInfoAlter->__invoke($libraries, 'foo');

    self::assertEquals($expected_libraries, $libraries);
  }

  /**
   * Tests that alteration is not attempted if there is no 'drupal.ajax'.
   */
  public function testAlterDrupalAjaxWithoutLibrary(): void {
    $libraries = [];

    $this->libraryInfoAlter->__invoke($libraries, 'core');

    self::assertEquals([], $libraries);
  }

  /**
   * Tests that additional JavaScript added to 'drupal.ajax'.
   */
  public function testAlterDrupalAjax(): void {
    $libraries = [
      'drupal.ajax' => [
        'js' => [],
      ],
    ];

    $this->libraryInfoAlter->__invoke($libraries, 'core');

    $expected_libraries = [
      'drupal.ajax' => [
        'js' => [
          '/modules/custom/niklan/assets/js/command.ajax.js' => [],
        ],
      ],
    ];
    self::assertEquals($expected_libraries, $libraries);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->libraryInfoAlter = $this->container->get(LibraryInfoAlter::class);
  }

}
