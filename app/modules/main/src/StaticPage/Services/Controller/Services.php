<?php

declare(strict_types=1);

namespace Drupal\app_main\StaticPage\Services\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\app_main\StaticPage\Services\Repository\ServicesSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Services implements ContainerInjectionInterface {

  public function __construct(
    private ServicesSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $settings = $container->get(ServicesSettings::class);
    \assert($settings instanceof ServicesSettings);

    return new self($settings);
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'app_main_services',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => ServicesSettings::TEXT_FORMAT,
      ],
      '#hourly_rate' => $this->settings->getHourlyRate(),
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($this->settings);
    $cache->applyTo($build);

    return $build;
  }

}
