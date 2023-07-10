<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Defines a base class for all functional tests.
 *
 * @group external_content
 */
abstract class ExternalContentTestBase extends BrowserTestBase {

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
