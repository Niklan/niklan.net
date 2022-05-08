<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Defines a base class for all kernel tests.
 */
abstract class NiklanTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'niklan',
    'media',
    'file',
    'image',
    'responsive_image',
    'comment',
    'block',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

}
