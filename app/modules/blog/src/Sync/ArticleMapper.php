<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync;

use Drupal\app_blog\Sync\Domain\ProcessedArticle;
use Drupal\app_contract\Contract\Node\Article;

final readonly class ArticleMapper {

  public function toEntity(ProcessedArticle $processed, Article $entity): void {
    $entity->setTitle($processed->title);
    $entity->set('body', $processed->description);
    $entity->set('field_media_image', $processed->posterMedia);

    $this->setAttachments($processed, $entity);
    $this->setNewFields($processed, $entity);
  }

  private function setAttachments(ProcessedArticle $processed, Article $entity): void {
    $entity->set('field_media_attachments', NULL);
    foreach ($processed->attachmentsMedia as $media) {
      $entity->get('field_media_attachments')->appendItem($media);
    }
  }

  private function setNewFields(ProcessedArticle $processed, Article $entity): void {
    $entity->set('field_content', [
      'value' => $processed->html,
      'format' => 'blog_article',
    ]);
    $entity->set('field_source_path_hash', $processed->sourcePathHash);
    $entity->set('field_estimated_read_time', $processed->estimatedReadTime);
  }

}
