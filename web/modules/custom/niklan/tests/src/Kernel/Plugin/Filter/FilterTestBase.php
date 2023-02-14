<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Filter;

use Drupal\filter\FilterPluginManager;
use Drupal\Tests\niklan\Functional\NiklanTestBase;

/**
 * Base class for all filter tests for the module.
 */
abstract class FilterTestBase extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'extra_field',
  ];

  /**
   * The filter plugin manager.
   */
  protected FilterPluginManager $filterManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->filterManager = $this->container->get('plugin.manager.filter');
  }

}
