<?php

declare(strict_types=1);

namespace Drupal\app_tag;

use Drupal\app_contract\Contract\Tag\TagRepository;
use Drupal\app_contract\Contract\Tag\TagUsageStatistics;
use Drupal\app_tag\Controller\TagList;
use Drupal\app_tag\EventSubscriber\RouteAlter;
use Drupal\app_tag\EventSubscriber\TermPageBuild;
use Drupal\app_tag\Repository\DatabaseTagRepository;
use Drupal\app_tag\Repository\DatabaseTagUsageStatistics;
use Drupal\app_tag\SiteMap\TagSiteMap;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

final readonly class AppTagServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    $autowire(DatabaseTagUsageStatistics::class);
    $autowire(DatabaseTagRepository::class);
    $autowire(TagList::class);

    $container
      ->setAlias(TagUsageStatistics::class, DatabaseTagUsageStatistics::class)
      ->setPublic(TRUE);
    $container
      ->setAlias(TagRepository::class, DatabaseTagRepository::class)
      ->setPublic(TRUE);

    $autowire(RouteAlter::class);
    $autowire(TermPageBuild::class);
    $autowire(TagSiteMap::class);
  }

}
