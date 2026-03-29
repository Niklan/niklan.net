<?php

declare(strict_types=1);

namespace Drupal\app_image;

use Drupal\app_image\Controller\DynamicImageStyleController;
use Drupal\app_image\DynamicImageStyle\DynamicImageStyle;
use Drupal\app_image\PathProcessor\DynamicImageStylePathProcessor;
use Drupal\app_image\Routing\RouteProvider;
use Drupal\app_image\Twig\DynamicImageStyleExtension;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\Definition;

final readonly class AppImageServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class): Definition => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    $container->setParameter('app_image.skip_procedural_hook_scan', TRUE);

    $autowire(DynamicImageStyle::class);
    $autowire(DynamicImageStyleController::class);
    $autowire(DynamicImageStylePathProcessor::class);
    $autowire(DynamicImageStyleExtension::class);
    $autowire(RouteProvider::class);
  }

}
