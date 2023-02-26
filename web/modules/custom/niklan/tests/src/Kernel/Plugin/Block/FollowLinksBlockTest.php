<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Url;

/**
 * Provides a test for copyright block.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Block\FollowLinksBlock
 */
final class FollowLinksBlockTest extends BlockTestBase {

  /**
   * Tests that block works as expected with default content.
   */
  public function testBlock(): void {
    $block_instance = $this
      ->blockManager
      ->createInstance('niklan_follow_links');
    \assert($block_instance instanceof BlockPluginInterface);
    $build = $block_instance->build();
    $this->render($build);

    self::assertRaw('https://niklan.net/blog.xml');
    self::assertRaw('https://t.me/niklannet');
    self::assertRaw('https://youtube.com/c/NiklanNet');
    self::assertRaw(Url::fromRoute('niklan.support')->getInternalPath());
  }

}
