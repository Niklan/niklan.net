<?php

declare(strict_types=1);

namespace Drupal\app_main;

use Drupal\app_main\Navigation\SiteMap\MainMenuSiteMap;
use Drupal\app_main\Navigation\Toolbar\ContentEditingToolbarLinksBuilder;
use Drupal\app_main\StaticPage\About\Repository\AboutSettings;
use Drupal\app_main\StaticPage\Contact\Repository\ContactSettings;
use Drupal\app_main\StaticPage\Home\Repository\HomeSettings;
use Drupal\app_main\StaticPage\Services\Repository\ServicesSettings;
use Drupal\app_main\StaticPage\Support\Repository\SupportSettings;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\Reference;

final readonly class AppMainServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    $autowire(HomeSettings::class);
    $autowire(AboutSettings::class);
    $autowire(ContactSettings::class);
    $autowire(ServicesSettings::class);
    $autowire(SupportSettings::class);

    $container->register(ContentEditingToolbarLinksBuilder::class)
      ->setPublic(TRUE)
      ->addArgument(new Reference('plugin.manager.menu.local_task'))
      ->addArgument(new Reference('current_route_match'));

    $container->autowire(MainMenuSiteMap::class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE)
      ->addTag('app_sitemap');
  }

}
