<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Contact\Repository;

use Drupal\app_contract\LanguageAwareStore\LanguageAwareSettingsStore;

final class ContactSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function getEmail(): string {
    $email = $this->getStore()->get('email', 'example@example.com');
    \assert(\is_string($email), 'Email must be a string.');

    return $email;
  }

  public function setEmail(string $email): self {
    $this->getStore()->set('email', $email);

    return $this;
  }

  public function getTelegram(): string {
    $telegram = $this->getStore()->get('telegram', 'https://t.me');
    \assert(\is_string($telegram), 'Telegram must be a string.');

    return $telegram;
  }

  public function setTelegram(string $url): self {
    $this->getStore()->set('telegram', $url);

    return $this;
  }

  public function getDescription(): string {
    $description = $this->getStore()->get('description', 'Additional information about how to contact the author.');
    \assert(\is_string($description), 'Description must be a string.');

    return $description;
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.contact_settings';
  }

}
