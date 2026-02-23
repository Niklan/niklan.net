<?php

declare(strict_types=1);

namespace Drupal\app_main\Routing;

use Drupal\app_main\SiteMap\Controller\SiteMapController;
use Drupal\app_main\StaticPage\About\Controller\About;
use Drupal\app_main\StaticPage\About\Form\AboutSettingsForm;
use Drupal\app_main\StaticPage\Contact\Controller\Contact;
use Drupal\app_main\StaticPage\Contact\Form\ContactSettingsForm;
use Drupal\app_main\StaticPage\Home\Controller\Home;
use Drupal\app_main\StaticPage\Home\Form\HomeSettingsForm;
use Drupal\app_main\StaticPage\Services\Controller\Services;
use Drupal\app_main\StaticPage\Services\Form\ServicesSettingsForm;
use Drupal\app_main\StaticPage\Support\Controller\Support;
use Drupal\app_main\StaticPage\Support\Form\SupportSettingsForm;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final readonly class RouteProvider {

  public function __invoke(): RouteCollection {
    $routes = new RouteCollection();

    $routes->add('app_main.home', new Route(
      path: '/home',
      defaults: [
        '_title' => 'Web Developer Blog',
        '_controller' => Home::class,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.home.settings', new Route(
      path: '/admin/app-main/home',
      defaults: [
        '_title' => 'Home page settings',
        '_form' => HomeSettingsForm::class,
      ],
      requirements: [
        '_permission' => 'administer site configuration',
      ],
      methods: ['GET', 'POST'],
    ));

    $routes->add('app_main.about', new Route(
      path: '/about',
      defaults: [
        '_title' => 'Niklan — freelance Drupal web-developer',
        '_controller' => About::class,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.about.settings', new Route(
      path: '/admin/app-main/about',
      defaults: [
        '_title' => 'About settings',
        '_form' => AboutSettingsForm::class,
      ],
      requirements: [
        '_permission' => 'administer site configuration',
      ],
      methods: ['GET', 'POST'],
    ));

    $routes->add('app_main.contact', new Route(
      path: '/contact',
      defaults: [
        '_title' => "Let's Talk",
        '_controller' => Contact::class,
      ],
      requirements: [
        '_permission' => 'access content',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.contact.settings', new Route(
      path: '/admin/app-main/contact',
      defaults: [
        '_title' => 'Contact settings',
        '_form' => ContactSettingsForm::class,
      ],
      requirements: [
        '_permission' => 'administer site configuration',
      ],
      methods: ['GET', 'POST'],
    ));

    $routes->add('app_main.services', new Route(
      path: '/services',
      defaults: [
        '_title' => "Let's Work Together",
        '_controller' => Services::class,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.services.settings', new Route(
      path: '/admin/app-main/services',
      defaults: [
        '_title' => 'Services settings',
        '_form' => ServicesSettingsForm::class,
      ],
      requirements: [
        '_permission' => 'administer site configuration',
      ],
      methods: ['GET', 'POST'],
    ));

    $routes->add('app_main.support', new Route(
      path: '/support',
      defaults: [
        '_title' => 'Support',
        '_controller' => Support::class,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.support.settings', new Route(
      path: '/admin/app-main/support',
      defaults: [
        '_title' => 'Support settings',
        '_form' => SupportSettingsForm::class,
      ],
      requirements: [
        '_permission' => 'administer site configuration',
      ],
      methods: ['GET', 'POST'],
    ));

    $routes->add('app_main.sitemap', new Route(
      path: '/sitemap',
      defaults: [
        '_title' => 'Site Map',
        '_controller' => SiteMapController::class,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.admin', new Route(
      path: '/admin/app-main',
      defaults: [
        '_title' => 'Niklan.net settings',
        '_controller' => 'Drupal\system\Controller\SystemController::overview',
        'link_id' => 'app_main.admin',
      ],
      requirements: [
        '_permission' => 'access administration pages',
      ],
      methods: ['GET'],
    ));

    $routes->add('app_main.admin_general', new Route(
      path: '/admin/app-main/general',
      defaults: [
        '_title' => 'General settings',
        '_controller' => 'Drupal\system\Controller\SystemController::systemAdminMenuBlockPage',
        'link_id' => 'app_main.admin_general',
      ],
      requirements: [
        '_permission' => 'access administration pages',
      ],
      methods: ['GET'],
    ));

    return $routes;
  }

}
