<?php

declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

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
    /** @var \Drupal\Core\Block\BlockPluginInterface $block_instance */
    $block_instance = $this
      ->blockManager
      ->createInstance('niklan_mobile_header_bar');
    $block_result = $block_instance->build();
    $block_html = (string) $this->renderer->renderRoot($block_result);

    $this->assertStringContainsString('Site logo', $block_html);
    $this->assertStringContainsString(
      Url::fromRoute('<front>')->setAbsolute()->toString(),
      $block_html,
    );
    $this->assertStringContainsString(
      'js-navigation-mobile-toggle',
      $block_html,
    );
  }

}
