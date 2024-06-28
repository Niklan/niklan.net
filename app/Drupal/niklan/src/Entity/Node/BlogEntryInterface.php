<?php

declare(strict_types=1);

namespace Drupal\niklan\Entity\Node;

/**
 * Defines an interface for 'blog_entry' bundle class.
 */
interface BlogEntryInterface extends NodeInterface {

  /**
   * Sets the external ID.
   *
   * @param string $external_id
   *   The external ID.
   */
  public function setExternalId(string $external_id): self;

  /**
   * Gets the external ID.
   */
  public function getExternalId(): string;

}
