<?php

declare(strict_types=1);

namespace Drupal\niklan\Contract\Repository;

interface AboutSettings {

  public const string TEXT_FORMAT = 'text';

  public function getPhotoMediaId(): ?string;

  public function setPhotoMediaId(?string $id): self;

  public function setTitle(string $title): self;

  public function getTitle(): string;

  public function setSubtitle(string $subtitle): self;

  public function getSubtitle(): string;

  public function setSummary(string $summary): self;

  public function getSummary(): string;

  public function setDescription(string $description): self;

  public function getDescription(): string;

}
