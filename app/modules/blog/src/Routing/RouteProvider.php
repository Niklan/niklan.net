<?php

declare(strict_types=1);

namespace Drupal\app_blog\Routing;

use Drupal\app_blog\Controller\BlogList;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final readonly class RouteProvider {

  public function __invoke(): RouteCollection {
    $routes = new RouteCollection();

    $routes->add('app_blog.blog_list', new Route(
      path: '/blog',
      defaults: [
        '_title' => 'Blog posts',
        '_controller' => BlogList::class,
        '_title_pager_suffix' => TRUE,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    return $routes;
  }

}
