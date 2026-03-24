<?php

declare(strict_types=1);

namespace Drupal\Tests\app_dev\Kernel\Hook\Toolbar;

use Drupal\app_dev\Hook\Toolbar\DevelopmentWarningToolbar;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DevelopmentWarningToolbar::class)]
final class DevelopmentWarningToolbarTest extends KernelTestBase {

  // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
  protected static $modules = ['toolbar', 'system', 'user'];

  /**
   * Provides a test for element when settings is enabled.
   */
  public function testWarning(): void {
    $implementation = new DevelopmentWarningToolbar($this->container->get('string_translation'));
    $result = $implementation();

    self::assertArrayHasKey('dev-site-warning', $result);
    self::assertArrayHasKey('tab', $result['dev-site-warning']);
  }

}
