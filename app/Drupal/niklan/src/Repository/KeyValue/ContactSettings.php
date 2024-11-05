<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository\KeyValue;

final class ContactSettings extends LanguageAwareSettingsStore {

  public function getEmail(): string {
    return $this->getStore()->get('email', 'example@example.com');
  }

  public function setEmail(string $email): self {
    $this->getStore()->set('email', $email);

    return $this;
  }

  public function getTelegram(): string {
    return $this->getStore()->get('telegram', 'https://t.me');
  }

  public function setTelegram(string $url): self {
    $this->getStore()->set('telegram', $url);

    return $this;
  }

  public function getDescription(): string {
    return $this->getStore()->get(
      key: 'description',
      default: 'Additional information about how to contact the author.',
    );
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
