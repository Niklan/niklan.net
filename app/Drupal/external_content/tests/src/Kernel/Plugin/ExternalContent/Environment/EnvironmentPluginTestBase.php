<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\Environment;

use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides an abstract for environment plugins test.
 */
abstract class EnvironmentPluginTestBase extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * {@selfdoc}
   */
  protected EnvironmentPluginManagerInterface $environmentPluginManager;

  /**
   * {@selfdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->environmentPluginManager = $this
      ->container
      ->get(EnvironmentPluginManagerInterface::class);
  }

}
