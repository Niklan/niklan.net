<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Defines a base class for all kernel tests.
 */
abstract class NiklanTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
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
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

}
