<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Defines a base class for all kernel tests.
 */
abstract class NiklanTestBase extends KernelTestBase {

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
    'app_file',
    'app_media',
    'app_platform',
    'app_tag',
    'breakpoint',
    'niklan',
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
