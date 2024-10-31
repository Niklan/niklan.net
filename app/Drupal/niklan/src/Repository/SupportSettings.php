<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class SupportSettings extends KeyValueSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function __construct(
    #[Autowire(service: 'keyvalue')]
    private readonly KeyValueFactoryInterface $keyValueFactory,
  ) {}

  public function setDescription(string $body): self {
    $this->getStore()->set('description', $body);

    return $this;
  }

  public function getDescription(): string {
    return $this->getStore()->get('description', 'The support page description.');
  }

  public function setDonateUrl(string $url): self {
    $this->getStore()->set('donate_url', $url);

    return $this;
  }

  public function getDonateUrl(): string {
    return $this->getStore()->get('donate_url', 'https://example.com');
  }

  #[\Override]
  protected function getKeyValueFactory(): KeyValueFactoryInterface {
    return $this->keyValueFactory;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.support_settings';
  }

}
