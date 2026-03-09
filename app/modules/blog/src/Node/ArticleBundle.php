<?php

declare(strict_types=1);

namespace Drupal\app_blog\Node;

use Drupal\app_contract\Contract\Node\Article;
use Drupal\app_contract\Node\NodeBundle;

/**
 * Provides a bundle class for 'blog_entry' content type.
 */
final class ArticleBundle extends NodeBundle implements Article {

  #[\Override]
  public function setExternalId(string $external_id): Article {
    $this->set('external_id', $external_id);
    return $this;
  }

  #[\Override]
  public function getExternalId(): string {
    return $this->get('external_id')->getString();
  }

  #[\Override]
  public function getCacheTagsToInvalidate(): array {
    $cache_tags = parent::getCacheTagsToInvalidate();

    $source_path_hash = $this->getSourcePathHash();
    if ($source_path_hash) {
      $cache_tags[] = 'blog_content:' . $source_path_hash;
    }

    return $cache_tags;
  }

  public function getContent(): ?string {
    if ($this->get('field_content')->isEmpty()) {
      return NULL;
    }

    return $this->get('field_content')->first()?->get('value')->getString();
  }

  public function getSourcePathHash(): ?string {
    if ($this->get('field_source_path_hash')->isEmpty()) {
      return NULL;
    }

    return $this->get('field_source_path_hash')->getString();
  }

  public function getEstimatedReadTime(): int {
    if ($this->get('field_estimated_read_time')->isEmpty()) {
      return 0;
    }

    return (int) $this->get('field_estimated_read_time')->getString();
  }

}
