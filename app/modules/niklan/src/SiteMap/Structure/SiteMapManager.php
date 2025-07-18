<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Structure;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class SiteMapManager implements SiteMapBuilderInterface {

  public function __construct(
    #[AutowireIterator('niklan.sitemap')]
    private iterable $siteMaps,
  ) {}

  #[\Override]
  public function build(): SiteMap {
    $sitemap = new SiteMap();

    foreach ($this->siteMaps as $sitemap_builder) {
      \assert($sitemap_builder instanceof SiteMapBuilderInterface);
      $sitemap->mergeSiteMap($sitemap_builder->build());
    }

    return $sitemap;
  }

}
