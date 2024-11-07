<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Repository\KeyValue\ContactSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class ContactController implements ContainerInjectionInterface {

  public function __construct(
    private ContactSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ContactSettings::class),
    );
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'niklan_contact',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => $this->settings::TEXT_FORMAT,
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
