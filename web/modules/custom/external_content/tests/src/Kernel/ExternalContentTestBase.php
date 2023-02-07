<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Defines a base class for all kernel tests.
 */
abstract class ExternalContentTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

}
