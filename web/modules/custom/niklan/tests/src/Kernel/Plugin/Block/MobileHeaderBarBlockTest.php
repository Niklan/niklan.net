<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Url;

/**
 * Provides a test for copyright block.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Block\MobileHeaderBarBlock
 */
final class MobileHeaderBarBlockTest extends BlockTestBase {

  /**
   * Tests that block works as expected with default content.
   */
  public function testBlock(): void {
    $block_instance = $this
      ->blockManager
      ->createInstance('niklan_mobile_header_bar');
    \assert($block_instance instanceof BlockPluginInterface);
    $build = $block_instance->build();
    $this->render($build);

    self::assertRaw('Site logo');
    self::assertRaw(Url::fromRoute('<front>')->setAbsolute()->toString());
    self::assertCount(1, $this->cssSelect('.js-navigation-mobile-toggle'));
  }

}
