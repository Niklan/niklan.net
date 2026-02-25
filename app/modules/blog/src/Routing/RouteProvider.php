<?php

declare(strict_types=1);

namespace Drupal\app_blog\Routing;

use Drupal\app_blog\Controller\BlogList;
use Drupal\app_blog\Controller\RssFeed;
use Drupal\app_blog\Controller\RssFeedRedirect;
use Drupal\app_blog\Controller\RssFeedStylesheet;
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

    $routes->add('app_blog.rss_feed', new Route(
      path: '/blog.xml',
      defaults: [
        '_controller' => RssFeed::class,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_blog.rss_redirect', new Route(
      path: '/rss.xml',
      defaults: [
        '_controller' => RssFeedRedirect::class,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_blog.rss_stylesheet', new Route(
      path: '/blog.xsl',
      defaults: [
        '_controller' => RssFeedStylesheet::class,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    return $routes;
  }

}
