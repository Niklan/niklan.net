<?php declare(strict_types = 1);

namespace Drupal\niklan\Repository;

/**
 * Defines an interface for content synchronization settings storage.
 *
 * @ingroup content_sync
 */
interface ContentSyncSettingsRepositoryInterface {

  /**
   * Sets the working directory with content.
   *
   * @param string|null $working_dir
   *   The URI for working dir.
   */
  public function setWorkingDir(?string $working_dir): self;

  /**
   * Gets working dir.
   *
   * @return string|null
   *   The URI for working dir.
   */
  public function getWorkingDir(): ?string;

}
