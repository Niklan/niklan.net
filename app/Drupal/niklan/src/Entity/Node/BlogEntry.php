<?php declare(strict_types = 1);

namespace Drupal\niklan\Entity\Node;

/**
 * Provides a bundle class for 'blog_entry' content type.
 */
final class BlogEntry extends Node implements BlogEntryInterface {

  /**
   * {@inheritdoc}
   */
  public function setExternalId(string $external_id): BlogEntryInterface {
    $this->set('external_id', $external_id);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getExternalId(): string {
    return $this->get('external_id')->getString();
  }

}
