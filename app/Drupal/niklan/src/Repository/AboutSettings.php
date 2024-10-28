<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class AboutSettings extends KeyValueSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function __construct(
    #[Autowire(service: 'keyvalue')]
    private readonly KeyValueFactoryInterface $keyValueFactory,
  ) {}

  public function getPhotoMediaId(): ?string {
    return $this->getStore()->get('photo_media_id');
  }

  public function setPhotoMediaId(?string $id): self {
    $id
      ? $this->getStore()->set('photo_media_id', $id)
      : $this->getStore()->delete('photo_media_id');

    return $this;
  }

  public function setTitle(string $title): self {
    $this->getStore()->set('title', $title);

    return $this;
  }

  public function getTitle(): string {
    return $this->getStore()->get('title', 'Hello, World!');
  }

  public function setSubtitle(string $subtitle): self {
    $this->getStore()->set('subtitle', $subtitle);

    return $this;
  }

  public function getSubtitle(): string {
    return $this->getStore()->get('subtitle', 'Beep-boop-beep');
  }

  public function setSummary(string $summary): self {
    $this->getStore()->set('summary', $summary);

    return $this;
  }

  public function getSummary(): string {
    return $this->getStore()->get('summary', 'The summary about an author.');
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  public function getDescription(): string {
    return $this->getStore()->get(
      key: 'description',
      default: 'The detailed description about an author.',
    );
  }

  #[\Override]
  protected function getKeyValueFactory(): KeyValueFactoryInterface {
    return $this->keyValueFactory;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.about_settings';
  }

}
