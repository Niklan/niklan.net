<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\ExtraField;

use Drupal\extra_field\Plugin\ExtraFieldDisplayInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayManagerInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Base class for all extra fields tests for the module.
 */
abstract class ExtraFieldTestBase extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'extra_field',
  ];

  /**
   * The extra field display plugin manager.
   */
  protected ?ExtraFieldDisplayManagerInterface $extraFieldDisplayManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->extraFieldDisplayManager = $this
      ->container
      ->get('plugin.manager.extra_field_display');
  }

  /**
   * Creates an instance for extra field display.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $configuration
   *   The plugin configuration.
   */
  protected function createExtraFieldDisplayInstance(string $plugin_id, array $configuration = []): ExtraFieldDisplayInterface {
    return $this
      ->extraFieldDisplayManager
      ->createInstance($plugin_id, $configuration);
  }

}
