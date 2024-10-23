<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\niklan\Contract\Repository\AboutSettings;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class AboutSettingsKeyValueStore implements AboutSettings {

  protected const string STORE_ID = 'niklan.about_settings';

  public function __construct(
    #[Autowire(service: 'keyvalue')]
    private KeyValueFactoryInterface $keyValueFactory,
  ) {}

  #[\Override]
  public function getPhotoMediaId(): ?string {
    return $this->store()->get('photo_media_id');
  }

  #[\Override]
  public function setPhotoMediaId(?string $id): self {
    $id
      ? $this->store()->set('photo_media_id', $id)
      : $this->store()->delete('photo_media_id');

    return $this;
  }

  #[\Override]
  public function setTitle(string $title): self {
    $this->store()->set('title', $title);

    return $this;
  }

  #[\Override]
  public function getTitle(): string {
    return $this->store()->get('title', 'Hello, World!');
  }

  #[\Override]
  public function setSubtitle(string $subtitle): self {
    $this->store()->set('subtitle', $subtitle);

    return $this;
  }

  #[\Override]
  public function getSubtitle(): string {
    return $this->store()->get('subtitle', '');
  }

  #[\Override]
  public function setSummary(string $summary): self {
    $this->store()->set('summary', $summary);

    return $this;
  }

  #[\Override]
  public function getSummary(): string {
    return $this->store()->get('summary', '');
  }

  #[\Override]
  public function setDescription(string $description): self {
    $this->store()->set('description', $description);

    return $this;
  }

  #[\Override]
  public function getDescription(): string {
    return $this->store()->get('description', '');
  }

  private function store(): KeyValueStoreInterface {
    return $this->keyValueFactory->get(self::STORE_ID);
  }

}
