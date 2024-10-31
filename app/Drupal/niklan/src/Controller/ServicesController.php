<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Repository\AboutSettings;
use Drupal\niklan\Repository\ServicesSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class ServicesController implements ContainerInjectionInterface {

  public function __construct(
    private ServicesSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ServicesSettings::class),
    );
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'niklan_services',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => AboutSettings::TEXT_FORMAT,
      ],
      '#hourly_rate' => $this->settings->getHourlyRate(),
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($this->settings);
    $cache->applyTo($build);

    return $build;
  }

}
