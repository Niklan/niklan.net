<?php

declare(strict_types=1);

namespace Drupal\app_blog\Node;

use Drupal\app_contract\Contract\Node\Article;
use Drupal\app_contract\Node\NodeBundle;
use Drupal\external_content\Nodes\Node as ContentNode;
use Drupal\app_blog\ExternalContent\Stages\ArticleTranslationFieldUpdater;
use Drupal\app_blog\ExternalContent\Utils\EstimatedReadTimeCalculator;

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
      $cache_tags[] = 'external_content:' . $source_path_hash;
    }

    return $cache_tags;
  }

  public function getContent(): ?string {
    if (!$this->hasField('field_content') || $this->get('field_content')->isEmpty()) {
      return NULL;
    }

    return $this->get('field_content')->getString();
  }

  public function getSourcePathHash(): ?string {
    if ($this->hasField('field_source_path_hash') && !$this->get('field_source_path_hash')->isEmpty()) {
      return $this->get('field_source_path_hash')->getString();
    }

    // Fallback to legacy external_content field.
    $data = $this->getExternalContentData();
    return $data[ArticleTranslationFieldUpdater::SOURCE_PATH_HASH_PROPERTY] ?? NULL;
  }

  public function getExternalContentData(): array {
    if (!$this->hasField('external_content') || $this->get('external_content')->isEmpty()) {
      return [];
    }

    $data = $this->get('external_content')->first()?->get('data')->getValue();
    \assert(\is_string($data));
    $result = \json_decode($data, TRUE);
    \assert(\is_array($result) || \is_null($result));

    return $result ?? [];
  }

  public function getEstimatedReadTime(): int {
    if ($this->hasField('field_estimated_read_time') && !$this->get('field_estimated_read_time')->isEmpty()) {
      return (int) $this->get('field_estimated_read_time')->getString();
    }

    // Fallback to legacy calculation from external_content.
    $content = $this->get('external_content')->first()?->get('content')->getValue();
    if (!$content instanceof ContentNode) {
      return 0;
    }
    return new EstimatedReadTimeCalculator()->calculateTotalTime($content);
  }

}
