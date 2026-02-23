<?php

declare(strict_types=1);

namespace Drupal\Tests\app_tag\Unit\Hook\Entity;

use Drupal\app_tag\Entity\TagBundle;
use Drupal\app_tag\Hook\Entity\BundleInfoAlter;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BundleInfoAlter::class)]
final class BundleInfoAlterTest extends UnitTestCase {

  public function testTagBundleClass(): void {
    $hook = new BundleInfoAlter();
    $bundles = [];

    $hook($bundles);

    self::assertSame(TagBundle::class, $bundles['taxonomy_term']['tags']['class']);
  }

  public function testExistingBundlesPreserved(): void {
    $hook = new BundleInfoAlter();
    $bundles = [
      'taxonomy_term' => [
        'categories' => [
          'class' => 'SomeClass',
          'label' => 'Category',
        ],
      ],
    ];

    $hook($bundles);

    self::assertArrayHasKey('categories', $bundles['taxonomy_term']);
    self::assertArrayHasKey('tags', $bundles['taxonomy_term']);
  }

}
