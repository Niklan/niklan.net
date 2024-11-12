<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Support\Repository;

use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;

final class SupportSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

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
  protected function getStoreId(): string {
    return 'niklan.support_settings';
  }

}
