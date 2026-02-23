<?php

declare(strict_types=1);

namespace Drupal\app_main\SiteMap\Controller;

use Drupal\app_contract\Contract\SiteMap\SiteMapBuilder;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class SiteMapController implements ContainerInjectionInterface {

  public function __construct(
    private SiteMapBuilder $siteMapManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $site_map_builder = $container->get(SiteMapBuilder::class);
    \assert($site_map_builder instanceof SiteMapBuilder);

    return new self($site_map_builder);
  }

  public function __invoke(): array {
    $sitemap = $this->siteMapManager->build();
    $build = [
      '#theme' => 'app_main_sitemap',
      '#sitemap' => $sitemap->toArray(),
    ];

    $cache = CacheableMetadata::createFromRenderArray($build);
    $cache->addCacheableDependency($sitemap);
    $cache->applyTo($build);

    return $build;
  }

}
