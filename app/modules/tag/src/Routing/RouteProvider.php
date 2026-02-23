<?php

declare(strict_types=1);

namespace Drupal\app_tag\Routing;

use Drupal\app_tag\Controller\TagList;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final readonly class RouteProvider {

  public function __invoke(): RouteCollection {
    $routes = new RouteCollection();

    $routes->add('app_tag.tag_list', new Route(
      path: '/tags',
      defaults: [
        '_title' => 'Tags',
        '_controller' => TagList::class,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    return $routes;
  }

}
