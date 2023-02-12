<?php

declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\niklan\EventSubscriber\RouteSubscriber;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides test for route subscriber.
 *
 * @coversDefaultClass \Drupal\niklan\EventSubscriber\RouteSubscriber
 */
final class RouteSubscriberTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that subscriber is alter route 'contact.site_page'.
   */
  public function testContactRouteAlter(): void {
    $subscribed_events = RouteSubscriber::getSubscribedEvents();
    $this->assertArrayHasKey(RoutingEvents::ALTER, $subscribed_events);

    $route_prophecy = $this->prophesize(Route::class);
    $route_prophecy->getDefault(Argument::any())->willReturn(NULL);
    $route_prophecy
      ->setDefault(Argument::type('string'), Argument::type('string'))
      ->will(static function ($args) use ($route_prophecy): Route {
        $route_prophecy->getDefault($args[0])->willReturn($args[1]);

        return $route_prophecy->reveal();
      });
    $route = $route_prophecy->reveal();

    $route_collection = $this->prophesize(RouteCollection::class);
    $route_collection->get('contact.site_page')->willReturn($route);

    $event = $this->prophesize(RouteBuildEvent::class);
    $event->getRouteCollection()->willReturn($route_collection->reveal());

    $route_subscriber = new RouteSubscriber();
    $route_subscriber->onAlterRoutes($event->reveal());

    $this->assertEquals(
      '\Drupal\niklan\Controller\StaticPagesController::contact',
      $route->getDefault('_controller'),
    );
  }

}
