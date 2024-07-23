<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

final class AboutSettingsRepository implements AboutSettingsRepositoryInterface {

  protected KeyValueStoreInterface $store;

  public function __construct(KeyValueFactoryInterface $key_value_factory) {
    $this->store = $key_value_factory->get('niklan.about_settings');
  }

  #[\Override]
  public function getPhotoMediaId(): ?string {
    return $this->store->get('photo_media_id');
  }

  #[\Override]
  public function setPhotoMediaId(?string $id): AboutSettingsRepositoryInterface {
    $id
      ? $this->store->set('photo_media_id', $id)
      : $this->store->delete('photo_media_id');

    return $this;
  }

  #[\Override]
  public function getPhotoResponsiveImageStyleId(): ?string {
    return $this->store->get('photo_responsive_image_style');
  }

  #[\Override]
  public function setPhotoResponsiveImageStyleId(?string $id): AboutSettingsRepositoryInterface {
    // Consider an empty string as NULL.
    $id
      ? $this->store->set('photo_responsive_image_style', $id)
      : $this->store->delete('photo_responsive_image_style');

    return $this;
  }

}
