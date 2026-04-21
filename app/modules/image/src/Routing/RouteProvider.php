<?php

declare(strict_types=1);

namespace Drupal\app_image\Routing;

use Drupal\app_image\Controller\DynamicImageStyleController;
use Drupal\Core\StreamWrapper\LocalStream;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Webmozart\Assert\Assert;

final readonly class RouteProvider {

  public function __construct(
    private StreamWrapperManagerInterface $streamWrapperManager,
  ) {}

  public function __invoke(): RouteCollection {
    $routes = new RouteCollection();

    $wrapper = $this->streamWrapperManager->getViaScheme('public');
    Assert::isInstanceOf($wrapper, LocalStream::class);
    $routes->add('app_image.dynamic_image_style', new Route(
      path: '/' . $wrapper->getDirectoryPath() . '/styles/dynamic',
      defaults: [
        '_controller' => DynamicImageStyleController::class,
        '_disable_route_normalizer' => TRUE,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      options: [
        'no_cache' => TRUE,
      ],
    ));

    $routes->add('app_image.dynamic_image_style_private', new Route(
      path: '/system/files/styles/dynamic',
      defaults: [
        '_controller' => DynamicImageStyleController::class,
        '_disable_route_normalizer' => TRUE,
      ],
      requirements: [
        '_access' => 'TRUE',
      ],
      options: [
        'no_cache' => TRUE,
      ],
    ));

    return $routes;
  }

}
