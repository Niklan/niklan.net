<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan_dev\Kernel\Hook\Toolbar;

use Drupal\niklan_dev\Hook\Toolbar\DevelopmentWarningToolbar;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides a test for a development warning in toolbar.
 *
 * @covers \Drupal\niklan_dev\Hook\Toolbar\DevelopmentWarningToolbar
 */
final class DevelopmentWarningToolbarTest extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['toolbar'];

  /**
   * Provides a test for element when settings is enabled.
   */
  public function testWarning(): void {
    $implementation = new DevelopmentWarningToolbar();
    $result = $implementation();

    self::assertArrayHasKey('dev-site-warning', $result);
    self::assertArrayHasKey('tab', $result['dev-site-warning']);
  }

}
