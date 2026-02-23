<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Repository;

use Drupal\app_contract\LanguageAwareStore\LanguageAwareSettingsStore;

final class PortfolioSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function setDescription(string $body): self {
    $this->getStore()->set('description', $body);

    return $this;
  }

  public function getDescription(): string {
    $description = $this->getStore()->get('description', 'The portfolio page description.');
    \assert(\is_string($description));

    return $description;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'app_portfolio.settings';
  }

}
