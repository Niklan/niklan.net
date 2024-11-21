<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Contact\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\StaticPage\Contact\Repository\HomeSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Contact implements ContainerInjectionInterface {

  public function __construct(
    private HomeSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(HomeSettings::class),
    );
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'niklan_contact',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => HomeSettings::TEXT_FORMAT,
      ],
      '#email' => $this->settings->getEmail(),
      '#telegram' => $this->settings->getTelegram(),
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($this->settings);
    $cache->applyTo($build);

    return $build;
  }

}
