<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Defines a base class for browser tests.
 */
abstract class NiklanTestBase extends WebDriverTestBase {

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
