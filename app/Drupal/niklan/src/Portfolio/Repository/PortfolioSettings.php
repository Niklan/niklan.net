<?php

declare(strict_types=1);

namespace Drupal\niklan\Portfolio\Repository;

use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;

final class PortfolioSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function setDescription(string $body): self {
    $this->getStore()->set('description', $body);

    return $this;
  }

  public function getDescription(): string {
    return $this->getStore()->get('description', 'The portfolio page description.');
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.portfolio_settings';
  }

}
