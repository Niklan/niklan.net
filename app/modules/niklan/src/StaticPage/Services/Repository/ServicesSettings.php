<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Services\Repository;

use Drupal\app_contract\LanguageAwareStore\LanguageAwareSettingsStore;

final class ServicesSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function getDescription(): string {
    // @phpstan-ignore-next-line return.type
    return $this->getStore()->get(
      key: 'description',
      default: 'Additional information about the services provided.',
    );
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  public function setHourlyRate(string $hourly_rate): self {
    $this->getStore()->set('hourly_rate', $hourly_rate);

    return $this;
  }

  public function getHourlyRate(): string {
    // @phpstan-ignore-next-line return.type
    return $this->getStore()->get('hourly_rate', '$1 000/hour');
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.services_settings';
  }

}
