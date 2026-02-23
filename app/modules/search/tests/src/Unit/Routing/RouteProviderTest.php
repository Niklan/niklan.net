<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Routing;

use Drupal\app_search\Controller\Search;
use Drupal\app_search\Routing\RouteProvider;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RouteProvider::class)]
final class RouteProviderTest extends UnitTestCase {

  public function testSearchPageRoute(): void {
    $provider = new RouteProvider();
    $routes = $provider();

    $route = $routes->get('app_search.search_page');
    self::assertNotNull($route);
    self::assertSame('/search', $route->getPath());
    self::assertSame(Search::class, $route->getDefault('_controller'));
    self::assertSame('Site search', $route->getDefault('_title'));
    self::assertSame('access content', $route->getRequirement('_permission'));
    self::assertSame(['GET'], $route->getMethods());
  }

  public function testRouteCount(): void {
    $provider = new RouteProvider();
    $routes = $provider();

    self::assertCount(1, $routes);
  }

}
