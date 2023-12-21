<?php declare(strict_types = 1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides a route subscriber.
 */
final class RouteSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      RoutingEvents::ALTER => ['onAlterRoutes', -200],
    ];
  }

  /**
   * Reacts on route alter.
   *
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   *   The route build event.
   */
  public function onAlterRoutes(RouteBuildEvent $event): void {
    $collection = $event->getRouteCollection();
    $route = $collection->get('contact.site_page');

    if (!$route) {
      return;
    }

    $route->setDefault('_title', "Let's Talk");
    $route->setDefault(
      '_controller',
      '\Drupal\niklan\Controller\StaticPagesController::contact',
    );
  }

}
