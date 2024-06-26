<?php

declare(strict_types=1);

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

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getCacheTagsToInvalidate(): array {
    $cache_tags = parent::getCacheTagsToInvalidate();

    $external_content_data = $this->getExternalContentData();

    if (\array_key_exists('pathname_md5', $external_content_data)) {
      $cache_tags[] = 'external_content:' . $external_content_data['pathname_md5'];
    }

    return $cache_tags;
  }

  /**
   * {@selfdoc}
   */
  public function getExternalContentData(): array {
    if (!$this->hasField('external_content') || $this->get('external_content')->isEmpty()) {
      return [];
    }

    $data = $this->get('external_content')->first()?->get('data')->getValue();

    return $data ? \json_decode($data, TRUE) : [];
  }

}
