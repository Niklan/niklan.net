<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

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
    /** @var \Drupal\Core\Block\BlockPluginInterface $block_instance */
    $block_instance = $this
      ->blockManager
      ->createInstance('niklan_follow_links');
    $block_result = $block_instance->build();
    $block_html = (string) $this->renderer->renderRoot($block_result);

    $this->assertStringContainsString(
      'https://niklan.net/blog.xml',
      $block_html,
    );
    $this->assertStringContainsString('https://t.me/niklannet', $block_html);
    $this->assertStringContainsString(
      'https://youtube.com/c/NiklanNet',
      $block_html,
    );
    $this->assertStringContainsString(
      Url::fromRoute('niklan.support')->setAbsolute()->toString(),
      $block_html,
    );
  }

}
