<?php

declare(strict_types=1);

namespace Drupal\Tests\app_platform\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Defines a base class for all kernel tests.
 */
abstract class AppPlatformTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'app_contract',
    'app_platform',
    'system',
    'user',
  ];

}
