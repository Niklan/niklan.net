<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Base class for all block tests for the module.
 */
abstract class BlockTestBase extends NiklanTestBase {

  /**
   * The block plugin manager.
   */
  protected BlockManagerInterface $blockManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->blockManager = $this->container->get('plugin.manager.block');
  }

}
