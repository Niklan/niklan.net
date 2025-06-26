<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\Blog\Contract\BlogRepository;
use Drupal\niklan\ExternalContent\Domain\Article;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslation;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleProcessPipeline;
use Drupal\niklan\Node\Entity\BlogEntryInterface;
use Drupal\niklan\Tag\Contract\TagRepository;
use Drupal\node\NodeStorageInterface;

final readonly class ArticleProcessor implements PipelineStage {

  private Pipeline $pipeline;
  private TagRepository $tagRepository;
  private BlogRepository $blogRepository;

  public function __construct() {
    $this->pipeline = new ArticleProcessPipeline();
    // @todo Use DI.
    $this->tagRepository = \Drupal::service(TagRepository::class);
    $this->blogRepository = \Drupal::service(BlogRepository::class);
  }

  public function process(PipelineContext $context): void {
    if (!$context instanceof SyncContext) {
      throw new \InvalidArgumentException('Invalid context');
    }
    foreach ($context->getArticles() as $article) {
      $this->processArticle($article, $context);
    }
  }

  private function processArticle(Article $article, SyncContext $context): void {
    $context->getLogger()->info('Processing article', [
      'article_id' => $article->id,
      'directory' => $article->directory,
    ]);

    $article_entity = $this->findOrCreateArticleEntity($article);
    if ($this->shouldSkipUpdate($article, $article_entity)) {
      $context->getLogger()->info('Skipping update, article not changed', [
        'article_id' => $article->id,
      ]);
      // return;.
    }

    $this->updateArticleMetadata($article, $article_entity);

    $translation = $article->getPrimaryTranslation();
    $this->processArticleTranslation($article, $translation, $article_entity, $context);

    foreach ($article->getTranslations() as $translation) {
      if ($translation->isPrimary || !$article_entity->isTranslatable()) {
        continue;
      }
      $this->processArticleTranslation($article, $translation, $article_entity, $context);
    }

    $article_entity->save();
  }

  private function shouldSkipUpdate(Article $article, BlogEntryInterface $article_entity): bool {
    if ($article_entity->isNew()) {
      return FALSE;
    }

    $article_updated = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC')->getTimestamp();
    return $article_updated > $article_entity->getChangedTime();
  }

  private function processArticleTranslation(Article $article, ArticleTranslation $translation, BlogEntryInterface $article_entity, SyncContext $context): void {
    $context->getLogger()->info('Processing article translation', [
      'article_id' => $article->id,
      'language' => $translation->language,
      'source_file' => $translation->sourcePath,
    ]);

    $article_entity = $article_entity->hasTranslation($translation->language)
      ? $article_entity->getTranslation($translation->language)
      : $article_entity->addTranslation($translation->language);

    $article_process_context = new ArticleTranslationProcessContext($article, $translation, $article_entity, $context);
    $this->pipeline->run($article_process_context);
  }

  private function getBlogStorage(): NodeStorageInterface {
    // @todo Replace by DI.
    return \Drupal::entityTypeManager()->getStorage('node');
  }

  private function findOrCreateArticleEntity(Article $article): BlogEntryInterface {
    $article_entity = $this->blogRepository->findByExternalId($article->id)
      ?? $this->getBlogStorage()->create(['type' => 'blog_entry']);

    if ($article_entity->isNew()) {
      $article_entity->setExternalId($article->id);
      $article_entity->setOwnerId(1);
    }

    // @phpstan-ignore-next-line
    return $article_entity;
  }

  private function updateArticleMetadata(Article $article, BlogEntryInterface $article_entity): void {
    $created_date = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->created, 'UTC');
    $article_entity->setCreatedTime($created_date->getTimestamp());

    $updated_date = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC');
    $article_entity->setChangedTime($updated_date->getTimestamp());

    $this->updateTags($article, $article_entity);
  }

  private function updateTags(Article $article, BlogEntryInterface $article_entity): void {
    $article_entity->set('field_tags', NULL);
    foreach ($article->tags as $tag) {
      $tag_entity = $this->tagRepository->findByExternalId($tag);
      if (!$tag_entity) {
        continue;
      }
      $article_entity->get('field_tags')->appendItem($tag_entity->id());
    }
  }

}
