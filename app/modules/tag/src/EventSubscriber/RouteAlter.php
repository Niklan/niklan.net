<?php

declare(strict_types=1);

namespace Drupal\app_tag\EventSubscriber;

use Drupal\app_tag\Controller\Tag;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class RouteAlter implements EventSubscriberInterface {

  public function onAlterRoutes(RouteBuildEvent $event): void {
    $route = $event->getRouteCollection()->get('entity.taxonomy_term.canonical');

    if (!$route) {
      return;
    }

    $route->setDefault('_title_pager_suffix', TRUE);
    $route->setDefault('_title_callback', Tag::class . '::title');
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      RoutingEvents::ALTER => ['onAlterRoutes', -210],
    ];
  }

}
