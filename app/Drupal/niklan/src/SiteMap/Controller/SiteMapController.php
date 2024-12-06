<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\SiteMap\Structure\SiteMapManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class SiteMapController implements ContainerInjectionInterface {

  public function __construct(
    private SiteMapManager $siteMapManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self($container->get(SiteMapManager::class));
  }

  public function __invoke(): array {
    $sitemap = $this->siteMapManager->build();
    $build = [
      '#theme' => 'niklan_sitemap',
      '#categories' => $sitemap->toArray(),
    ];

    $cache = CacheableMetadata::createFromRenderArray($build);
    $cache->addCacheableDependency($sitemap);
    $cache->applyTo($build);

    return $build;
  }

}
