<?php

declare(strict_types=1);

namespace Drupal\app_comment\Routing;

use Drupal\app_comment\Telegram\Controller\WebhookController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final readonly class RouteProvider {

  public function __invoke(): RouteCollection {
    $collection = new RouteCollection();

    $route = new Route('/api/telegram/webhook');
    $route->setMethods(['POST']);
    $route->setDefault('_controller', WebhookController::class);
    $route->setRequirement('_access', 'TRUE');
    $collection->add('app_comment.telegram.webhook', $route);

    return $collection;
  }

}
