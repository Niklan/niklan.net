<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\niklan\Comment\Controller\CommentReply;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class RouteAlter implements EventSubscriberInterface {

  public function onAlterRoutes(RouteBuildEvent $event): void {
    $collection = $event->getRouteCollection();
    $reply_route = $collection->get('comment.reply');

    if (!$reply_route) {
      return;
    }

    $reply_route->setDefault('_controller', CommentReply::class);
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      RoutingEvents::ALTER => 'onAlterRoutes',
    ];
  }

}
