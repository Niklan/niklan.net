<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository\KeyValue;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\niklan\Contract\Factory\KeyValue\LanguageAwareFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class LanguageAwareSettingsStore implements CacheableDependencyInterface {

  public const string TEXT_FORMAT = 'text';

  private ?string $currentLanguage = NULL;

  abstract protected function getStoreId(): string;

  public function __construct(
    #[Autowire(service: 'keyvalue.language_aware')]
    private readonly LanguageAwareFactory $factory,
  ) {}

  public function changeLanguageCode(?string $language_code): self {
    $this->currentLanguage = $language_code;

    return $this;
  }

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
    return $this->factory->get(
      collection: $this->getStoreId(),
      language_code: $this->currentLanguage,
    );
  }

}
