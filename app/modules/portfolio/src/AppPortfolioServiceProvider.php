<?php

declare(strict_types=1);

namespace Drupal\app_portfolio;

use Drupal\app_portfolio\Controller\PortfolioList;
use Drupal\app_portfolio\Repository\PortfolioSettings;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

final readonly class AppPortfolioServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    $autowire(PortfolioSettings::class);
    $autowire(PortfolioList::class);
  }

}
