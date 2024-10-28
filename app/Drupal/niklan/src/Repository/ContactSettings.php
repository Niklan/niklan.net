<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ContactSettings extends KeyValueSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function __construct(
    #[Autowire(service: 'keyvalue')]
    private readonly KeyValueFactoryInterface $keyValueFactory,
  ) {}

  public function getEmail(): string {
    return $this->getStore()->get('email', 'example@example.com');
  }

  public function setEmail(string $email): self {
    $this->getStore()->set('email', $email);

    return $this;
  }

  public function getTelegram(): string {
    return $this->getStore()->get('telegram', 'https://t.me');
  }

  public function setTelegram(string $url): self {
    $this->getStore()->set('telegram', $url);

    return $this;
  }

  public function getDescription(): string {
    return $this->getStore()->get(
      key: 'description',
      default: 'Additional information about how to contact the author.',
    );
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  #[\Override]
  protected function getKeyValueFactory(): KeyValueFactoryInterface {
    return $this->keyValueFactory;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.contact_settings';
  }

}
