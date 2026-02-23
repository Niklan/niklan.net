<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Functional\Plugin\Filter;

use Drupal\filter\FilterPluginManager;
use Drupal\Tests\app_blog\Functional\AppBlogTestBase;

/**
 * Base class for all filter tests for the module.
 */
abstract class FilterTestBase extends AppBlogTestBase {

  protected FilterPluginManager $filterManager;

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->filterManager = $this->container->get('plugin.manager.filter');
  }

}
