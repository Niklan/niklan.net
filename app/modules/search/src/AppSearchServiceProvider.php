<?php

declare(strict_types=1);

namespace Drupal\app_search;

use Drupal\app_search\Controller\Search;
use Drupal\app_search\Repository\GlobalSearch;
use Drupal\app_search\Repository\SearchApiSearch;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

final readonly class AppSearchServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(boolean: TRUE)
      ->setAutoconfigured(autoconfigured: TRUE);

    $container->autowire(SearchApiSearch::class)->setAbstract(boolean: TRUE);
    $container
      ->registerChild(GlobalSearch::class, SearchApiSearch::class)
      ->setPublic(boolean: TRUE)
      ->setAutoconfigured(autoconfigured: TRUE);

    $autowire(Search::class);
  }

}
