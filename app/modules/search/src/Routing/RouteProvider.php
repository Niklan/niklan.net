<?php

declare(strict_types=1);

namespace Drupal\app_search\Routing;

use Drupal\app_search\Controller\Search;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final readonly class RouteProvider {

  public function __invoke(): RouteCollection {
    $routes = new RouteCollection();

    $routes->add('app_search.search_page', new Route(
      path: '/search',
      defaults: [
        '_controller' => Search::class,
        '_title' => 'Site search',
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    return $routes;
  }

}
