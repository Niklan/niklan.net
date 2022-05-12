<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\niklan\Kernel\NiklanExternalContentTestBase;

/**
 * Base class for all block tests for the module.
 */
abstract class BlockExternalContentTestBase extends NiklanExternalContentTestBase {

  /**
   * The block plugin manager.
   */
  protected ?BlockManagerInterface $blockManager;

  /**
   * The renderer.
   */
  protected ?RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->blockManager = $this->container->get('plugin.manager.block');
    $this->renderer = $this->container->get('renderer');
  }

}
