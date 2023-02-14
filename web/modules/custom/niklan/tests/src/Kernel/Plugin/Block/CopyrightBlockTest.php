<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

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
    /** @var \Drupal\Core\Block\BlockPluginInterface $block_instance */
    $block_instance = $this->blockManager->createInstance('niklan_copyright');
    $block_result = $block_instance->build();
    $block_html = (string) $this->renderer->renderRoot($block_result);

    $this->assertStringContainsString('CC-BY-SA 4.0', $block_html);
    $this->assertStringContainsString('©', $block_html);
    $this->assertStringContainsString('Niklan', $block_html);
  }

}
