<?php

declare(strict_types=1);

namespace Drupal\Tests\app_main\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Defines a base class for all kernel tests.
 */
abstract class AppMainTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'app_blog',
    'app_contract',
    'app_main',
    'app_platform',
    'app_tag',
    'breakpoint',
    'node',
    'media',
    'file',
    'field',
    'image',
    'responsive_image',
    'comment',
    'system',
    'user',
    'block',
    'taxonomy',
    'text',
    'filter',
    'search_api',
    'twig_tweak',
    'external_content',
    'photoswipe',
  ];

}
