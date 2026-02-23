<?php

declare(strict_types=1);

namespace Drupal\app_comment\Comment\EventSubscriber;

use Drupal\app_comment\Comment\Controller\CommentReply;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
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
