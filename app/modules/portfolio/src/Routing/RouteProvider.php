<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Routing;

use Drupal\app_portfolio\Controller\PortfolioList;
use Drupal\app_portfolio\Form\PortfolioSettingsForm;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final readonly class RouteProvider {

  public function __invoke(): RouteCollection {
    $routes = new RouteCollection();

    $routes->add('app_portfolio.portfolio_list', new Route(
      path: '/portfolio',
      defaults: [
        '_title' => 'Portfolio',
        '_controller' => PortfolioList::class,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_portfolio.portfolio.settings', new Route(
      path: '/admin/niklan/portfolio',
      defaults: [
        '_title' => 'Portfolio settings',
        '_form' => PortfolioSettingsForm::class,
      ],
      requirements: [
        '_permission' => 'administer site configuration',
      ],
      methods: ['GET', 'POST'],
    ));

    return $routes;
  }

}
