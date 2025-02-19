<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\About\Repository;

use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;

final class AboutSettings extends LanguageAwareSettingsStore {

  public const string TEXT_FORMAT = 'text';

  public function getPhotoMediaId(): ?string {
    $photo_media_id = $this->getStore()->get('photo_media_id');
    \assert(\is_string($photo_media_id) || \is_null($photo_media_id), 'Photo media ID must be a string or null.');

    return $photo_media_id;
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
    $title = $this->getStore()->get('title', "I'm an alien 👽");
    \assert(\is_string($title), 'Title must be a string.');

    return $title;
  }

  public function setSubtitle(string $subtitle): self {
    $this->getStore()->set('subtitle', $subtitle);

    return $this;
  }

  public function getSubtitle(): string {
    /* cSpell:ignore traveller */
    $subtitle = $this->getStore()->get('subtitle', 'Greetings traveller!');
    \assert(\is_string($subtitle), 'Subtitle must be a string.');

    return $subtitle;
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

    $summary = $this->getStore()->get('summary', $default);
    \assert(\is_string($summary), 'Summary must be a string.');

    return $summary;
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

    $description = $this->getStore()->get('description', $default);
    \assert(\is_string($description), 'Description must be a string.');

    return $description;
  }

  #[\Override]
  protected function getStoreId(): string {
    return 'niklan.about_settings';
  }

}
