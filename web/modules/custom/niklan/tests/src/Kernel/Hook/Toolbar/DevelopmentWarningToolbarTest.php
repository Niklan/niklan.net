<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Hook\Toolbar;

use Drupal\niklan\Hook\Toolbar\DevelopmentWarningToolbar;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides a test for a development warning in toolbar.
 *
 * @covers \Drupal\niklan\Hook\Toolbar\DevelopmentWarningToolbar
 */
final class DevelopmentWarningToolbarTest extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['toolbar'];

  /**
   * Provides a test for element when settings is disabled (default).
   */
  public function testDisabledElement(): void {
    $implementation = new DevelopmentWarningToolbar();
    $result = $implementation();

    self::assertEmpty($result);
  }

  /**
   * Provides a test for element when settings is enabled.
   */
  public function testEnabledElement(): void {
    $this->setSetting('niklan_development_warning', TRUE);

    $implementation = new DevelopmentWarningToolbar();
    $result = $implementation();

    self::assertArrayHasKey('dev-site-warning', $result);
    self::assertArrayHasKey('tab', $result['dev-site-warning']);
  }

}
