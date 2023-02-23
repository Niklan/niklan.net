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
    $block_result = $block_instance->build();
    $block_html = (string) $this->renderer->renderRoot($block_result);

    self::assertStringContainsString(
      'https://niklan.net/blog.xml',
      $block_html,
    );
    self::assertStringContainsString('https://t.me/niklannet', $block_html);
    self::assertStringContainsString(
      'https://youtube.com/c/NiklanNet',
      $block_html,
    );
    self::assertStringContainsString(
      Url::fromRoute('niklan.support')->setAbsolute()->toString(),
      $block_html,
    );
  }

}
