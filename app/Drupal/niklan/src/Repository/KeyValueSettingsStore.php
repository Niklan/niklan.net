<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

abstract class KeyValueSettingsStore implements CacheableDependencyInterface {

  abstract protected function getKeyValueFactory(): KeyValueFactoryInterface;

  abstract protected function getStoreId(): string;

  #[\Override]
  public function getCacheContexts(): array {
    return [];
  }

  #[\Override]
  public function getCacheTags(): array {
    return [$this->getStoreId()];
  }

  #[\Override]
  public function getCacheMaxAge(): int {
    return CacheBackendInterface::CACHE_PERMANENT;
  }

  protected function getStore(): KeyValueStoreInterface {
    return $this->getKeyValueFactory()->get($this->getStoreId());
  }

}
