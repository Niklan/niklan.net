<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a test for copyright block.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Block\CopyrightBlock
 */
final class CopyrightBlockTest extends BlockTestBase {

  /**
   * Tests that block works as expected with default content.
   */
  public function testBlock(): void {
    $block_instance = $this->blockManager->createInstance('niklan_copyright');
    \assert($block_instance instanceof BlockPluginInterface);
    $block_result = $block_instance->build();
    $block_html = (string) $this->renderer->renderRoot($block_result);

    self::assertStringContainsString('CC-BY-SA 4.0', $block_html);
    self::assertStringContainsString('©', $block_html);
    self::assertStringContainsString('Niklan', $block_html);
  }

}
