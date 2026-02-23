<?php

declare(strict_types=1);

namespace Drupal\Tests\app_portfolio\Unit\Routing;

use Drupal\app_portfolio\Controller\PortfolioList;
use Drupal\app_portfolio\Form\PortfolioSettingsForm;
use Drupal\app_portfolio\Routing\RouteProvider;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RouteProvider::class)]
final class RouteProviderTest extends UnitTestCase {

  public function testPortfolioListRoute(): void {
    $provider = new RouteProvider();
    $routes = $provider();

    $route = $routes->get('app_portfolio.portfolio_list');
    self::assertNotNull($route);
    self::assertSame('/portfolio', $route->getPath());
    self::assertSame(PortfolioList::class, $route->getDefault('_controller'));
    self::assertSame('Portfolio', $route->getDefault('_title'));
    self::assertSame('access content', $route->getRequirement('_permission'));
    self::assertSame(['GET'], $route->getMethods());
  }

  public function testPortfolioSettingsRoute(): void {
    $provider = new RouteProvider();
    $routes = $provider();

    $route = $routes->get('app_portfolio.portfolio.settings');
    self::assertNotNull($route);
    self::assertSame('/admin/niklan/portfolio', $route->getPath());
    self::assertSame(PortfolioSettingsForm::class, $route->getDefault('_form'));
    self::assertSame('Portfolio settings', $route->getDefault('_title'));
    self::assertSame('administer site configuration', $route->getRequirement('_permission'));
    self::assertSame(['GET', 'POST'], $route->getMethods());
  }

  public function testRouteCount(): void {
    $provider = new RouteProvider();
    $routes = $provider();

    self::assertCount(2, $routes);
  }

}
