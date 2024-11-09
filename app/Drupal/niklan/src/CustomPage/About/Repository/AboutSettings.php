<?php

declare(strict_types=1);

namespace Drupal\niklan\CustomPage\About\Repository;

use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;

final class AboutSettings extends LanguageAwareSettingsStore {

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
    return $this->getStore()->get('title', "I'm an alien 👽");
  }

  public function setSubtitle(string $subtitle): self {
    $this->getStore()->set('subtitle', $subtitle);

    return $this;
  }

  public function getSubtitle(): string {
    return $this->getStore()->get('subtitle', 'Greetings traveller!');
  }

  public function setSummary(string $summary): self {
    $this->getStore()->set('summary', $summary);

    return $this;
  }

  public function getSummary(): string {
    $default = <<<'HTML'
    <p>My name is X-1234, and I came to Earth from a distant galaxy. I am here
     to share with you my knowledge and experience, as well as tell you about
     how I see the world around us.</p>
    HTML;

    return $this->getStore()->get('summary', $default);
  }

  public function setDescription(string $description): self {
    $this->getStore()->set('description', $description);

    return $this;
  }

  public function getDescription(): string {
    $default = <<<'HTML'
    <p>I love exploring new cultures, exploring nature, and connecting with
     people. I'm interested to know how you live, what your dreams are, and what
     your values are. I hope that my stories and reflections will help you see
     the world in a new way and perhaps even find answers to some of your
     questions.</p>

    <p>Join me on this exciting journey through the universe and Earth!</p>
    HTML;

    return $this->getStore()->get('description', $default);
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.about_settings';
  }

}
