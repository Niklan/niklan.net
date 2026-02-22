<?php

declare(strict_types=1);

namespace Drupal\niklan\LanguageAwareStore\Repository;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\app_contract\Contract\LanguageAwareStore\LanguageAwareFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class LanguageAwareSettingsStore implements CacheableDependencyInterface {

  private ?string $currentLanguage = NULL;

  abstract protected function getStoreId(): string;

  public function __construct(
    #[Autowire(service: 'keyvalue.language_aware')]
    private readonly LanguageAwareFactory $factory,
    private readonly RouteMatchInterface $routeMatch,
  ) {}

  public function changeLanguageCode(?string $language_code): self {
    $this->currentLanguage = $language_code;

    return $this;
  }

  #[\Override]
  public function getCacheContexts(): array {
    return ['languages:language_interface'];
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
    $this->lookupLanguageFromRoute();

    return $this->factory->get(
      collection: $this->getStoreId(),
      language_code: $this->currentLanguage,
    );
  }

  private function lookupLanguageFromRoute(): void {
    // Only attempt to resolve the language from the route if it has not already
    // been set.
    if ($this->currentLanguage) {
      return;
    }

    $language = $this->routeMatch->getParameter('key_value_language_aware_code');
    \assert(\is_string($language) || \is_null($language));
    $this->currentLanguage = $language;
  }

}
