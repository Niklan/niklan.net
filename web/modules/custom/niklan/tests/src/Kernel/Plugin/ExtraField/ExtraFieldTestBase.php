<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\ExtraField;

use Drupal\Core\Render\RendererInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayManagerInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Base class for all extra fields tests for the module.
 */
abstract class ExtraFieldTestBase extends NiklanTestBase {

  /**
   * The extra field display plugin manager.
   */
  protected ?ExtraFieldDisplayManagerInterface $extraFieldDisplayManager;

  /**
   * The renderer.
   */
  protected ?RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'extra_field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->extraFieldDisplayManager = $this->container->get('plugin.manager.extra_field_display');
    $this->renderer = $this->container->get('renderer');
  }

}
