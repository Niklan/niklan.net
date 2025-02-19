<?php

declare(strict_types=1);

namespace Drupal\niklan\Node\Entity;

use Drupal\external_content\Contract\Node\NodeInterface as ContentNodeInterface;
use Drupal\niklan\ExternalContent\Utils\EstimatedReadTimeCalculator;

/**
 * Provides a bundle class for 'blog_entry' content type.
 */
final class BlogEntry extends Node implements BlogEntryInterface {

  #[\Override]
  public function setExternalId(string $external_id): BlogEntryInterface {
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

    $external_content_data = $this->getExternalContentData();

    if (\array_key_exists('pathname_md5', $external_content_data)) {
      $cache_tags[] = 'external_content:' . $external_content_data['pathname_md5'];
    }

    return $cache_tags;
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
    $content = $this
      ->get('external_content')
      ->first()
      ?->get('content')
        ->getValue();

    if (!$content instanceof ContentNodeInterface) {
      return 0;
    }

    $calculator = new EstimatedReadTimeCalculator();

    return $calculator->calculate($content);
  }

}
