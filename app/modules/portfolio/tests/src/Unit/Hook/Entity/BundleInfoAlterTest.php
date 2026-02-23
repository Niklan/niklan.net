<?php

declare(strict_types=1);

namespace Drupal\Tests\app_portfolio\Unit\Hook\Entity;

use Drupal\app_portfolio\Hook\Entity\BundleInfoAlter;
use Drupal\app_portfolio\Node\PortfolioBundle;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BundleInfoAlter::class)]
final class BundleInfoAlterTest extends UnitTestCase {

  public function testPortfolioBundleClass(): void {
    $hook = new BundleInfoAlter();
    $bundles = [];

    $hook($bundles);

    self::assertSame(PortfolioBundle::class, $bundles['node']['portfolio']['class']);
  }

  public function testExistingBundlesPreserved(): void {
    $hook = new BundleInfoAlter();
    $bundles = [
      'node' => [
        'article' => [
          'class' => 'SomeClass',
          'label' => 'Article',
        ],
      ],
    ];

    $hook($bundles);

    self::assertArrayHasKey('article', $bundles['node']);
    self::assertArrayHasKey('portfolio', $bundles['node']);
  }

}
