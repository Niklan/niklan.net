<?php

declare(strict_types = 1);

namespace Drupal\niklan\Repository;

/**
 * Defines an interface for about settings storage.
 */
interface AboutSettingsRepositoryInterface {

  /**
   * Gets a photo media entity ID.
   *
   * @return string
   *   The media entity ID.
   */
  public function getPhotoMediaId(): ?string;

  /**
   * Sets a photo media entity ID.
   *
   * @param string|null $id
   *   The media entity ID.
   *
   * @return $this
   */
  public function setPhotoMediaId(?string $id): self;

  /**
   * Gets a responsive image style ID.
   *
   * @return string|null
   *   The responsive image style ID.
   */
  public function getPhotoResponsiveImageStyleId(): ?string;

  /**
   * Sets a responsive image style ID.
   *
   * @param string|null $id
   *   The responsive image style ID.
   *
   * @return $this
   */
  public function setPhotoResponsiveImageStyleId(?string $id): self;

}
