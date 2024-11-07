<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Repository\KeyValue\SupportSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class SupportController implements ContainerInjectionInterface {

  public function __construct(
    private SupportSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(SupportSettings::class),
    );
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'niklan_support',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => $this->settings::TEXT_FORMAT,
      ],
      '#donate_url' => $this->settings->getDonateUrl(),
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($this->settings);
    $cache->applyTo($build);

    return $build;
  }

}
