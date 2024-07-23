<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

interface AboutSettingsRepositoryInterface {

  public function getPhotoMediaId(): ?string;

  public function setPhotoMediaId(?string $id): self;

  public function getPhotoResponsiveImageStyleId(): ?string;

  public function setPhotoResponsiveImageStyleId(?string $id): self;

}
