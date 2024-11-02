<?php

declare(strict_types=1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\StorageTransformEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides a settings configuration export/import subscriber.
 *
 * This event subscriber is designed to exclude configurations related to
 * settings from being imported and exported during configuration sync. These
 * configurations are specific to each environment and should not be overridden
 * during deployment or after any changes in the development environment.
 */
final readonly class SettingsConfigSubscriber implements EventSubscriberInterface {

  private const CONFIG_NAMES = [
    'niklan.about_settings',
  ];

  public function __construct(
    #[Autowire(service: 'config.storage')]
    private StorageInterface $activeStorage,
  ) {}

  public function onImport(StorageTransformEvent $event): void {
    $storage = $event->getStorage();

    foreach ($this->getCollections($storage) as $collection_name) {
      $collection = $storage->createCollection($collection_name);
      $active_collection = $this
        ->activeStorage
        ->createCollection($collection_name);
      $this->processConfigurations($collection, $active_collection);
    }
  }

  public function onExport(StorageTransformEvent $event): void {
    $storage = $event->getStorage();

    foreach ($this->getCollections($storage) as $collection_name) {
      $collection = $storage->createCollection($collection_name);
      $this->processConfigurations($collection);
    }
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      'config.transform.import' => ['onImport', -500],
      'config.transform.export' => ['onExport', 500],
    ];
  }

  private function processConfigurations(StorageInterface $collection, ?StorageInterface $active_collection = NULL): void {
    foreach (self::CONFIG_NAMES as $config_name) {
      $collection->delete($config_name);

      if (!$active_collection || $collection->exists($config_name) || !$active_collection->exists($config_name)) {
        continue;
      }

      // If the active collection is specified, it means that this is an import
      // operation and we need to keep the existing values in the database.
      // Otherwise, they will be deleted.
      $collection->write($config_name, $active_collection->read($config_name));
    }
  }

  private function getCollections(StorageInterface $storage): array {
    return \array_merge(
      [StorageInterface::DEFAULT_COLLECTION],
      $storage->getAllCollectionNames(),
    );
  }

}
