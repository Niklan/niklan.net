<?php

declare(strict_types=1);

namespace Drupal\app_main\StaticPage\Support\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\app_main\StaticPage\Support\Repository\SupportSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Support implements ContainerInjectionInterface {

  public function __construct(
    private SupportSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $settings = $container->get(SupportSettings::class);
    \assert($settings instanceof SupportSettings);

    return new self($settings);
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'app_main_support',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => SupportSettings::TEXT_FORMAT,
      ],
      '#donate_url' => $this->settings->getDonateUrl(),
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($this->settings);
    $cache->applyTo($build);

    return $build;
  }

}
