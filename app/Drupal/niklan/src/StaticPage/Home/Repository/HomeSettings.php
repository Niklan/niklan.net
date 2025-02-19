<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Home\Repository;

use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;

final class HomeSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function getHeading(): string {
    $heading = $this->getStore()->get('heading', 'Web Developer Blog');
    \assert(\is_string($heading), 'The heading must be a string.');

    return $heading;
  }

  public function setHeading(string $heading): self {
    $this->getStore()->set('heading', $heading);

    return $this;
  }

  public function getDescription(): string {
    $description = $this->getStore()->get('description', 'The homepage description.');
    \assert(\is_string($description), 'The description must be a string.');

    return $description;
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  public function setCards(array $cards): self {
    $this->getStore()->set('cards', $cards);

    return $this;
  }

  /**
   * @return array{}|array<int, array{
   *   media_id: string,
   *    title: string,
   *    description: string,
   *   }> */
  public function getCards(): array {
    $cards = $this->getStore()->get('cards', []);
    \assert(\is_array($cards), 'The cards must be an array.');

    return $cards;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.home_settings';
  }

}
