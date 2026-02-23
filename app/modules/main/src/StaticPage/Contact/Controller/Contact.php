<?php

declare(strict_types=1);

namespace Drupal\app_main\StaticPage\Contact\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\app_main\StaticPage\Contact\Repository\ContactSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Contact implements ContainerInjectionInterface {

  public function __construct(
    private ContactSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $settings = $container->get(ContactSettings::class);
    \assert($settings instanceof ContactSettings);

    return new self($settings);
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'app_main_contact',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => ContactSettings::TEXT_FORMAT,
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
