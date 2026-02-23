<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\app_main\StaticPage\Contact\Repository\ContactSettings;
use Psr\Container\ContainerInterface;

final readonly class PreprocessMaintenancePage implements ContainerInjectionInterface {

  public function __construct(
    private ContactSettings $contactSettings,
  ) {}

  public static function create(ContainerInterface $container): self {
    $contact_settings = $container->get(ContactSettings::class);
    \assert($contact_settings instanceof ContactSettings);

    return new self($contact_settings);
  }

  public function __invoke(array &$variables): void {
    $cache = CacheableMetadata::createFromRenderArray($variables);
    $cache->addCacheableDependency($this->contactSettings);
    $cache->applyTo($variables);

    $variables['links'] = [
      [
        'title' => $this->contactSettings->getEmail(),
        'url' => 'mailto:' . $this->contactSettings->getEmail(),
      ],
      [
        'title' => 'Telegram',
        'url' => $this->contactSettings->getTelegram(),
      ],
    ];
  }

}
