<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Defines a base class for all browser tests.
 */
abstract class AppBlogTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'app_blog',
    'app_platform',
    'app_tag',
    'image',
    'responsive_image',
    'block',
    'external_content',
    'photoswipe',
    'taxonomy',
  ];

}
