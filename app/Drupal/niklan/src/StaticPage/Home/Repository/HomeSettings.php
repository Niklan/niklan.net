<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Home\Repository;

use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;

final class HomeSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function getHeading(): string {
    return $this->getStore()->get('heading', 'Web Developer Blog');
  }

  public function setHeading(string $heading): self {
    $this->getStore()->set('heading', $heading);

    return $this;
  }

  public function getDescription(): string {
    return $this->getStore()->get(
      key: 'description',
      default: 'The homepage description.',
    );
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  public function setCards(array $cards): self {
    $this->getStore()->set('cards', $cards);

    return $this;
  }

  public function getCards(): array {
    return $this->getStore()->get('cards', []);
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.home_settings';
  }

}
