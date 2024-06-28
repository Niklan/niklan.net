<?php

declare(strict_types=1);

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
    $build = $block_instance->build();
    $this->render($build);

    self::assertRaw('CC-BY-SA 4.0');
    self::assertRaw('©');
    self::assertRaw('Niklan');
  }

}
