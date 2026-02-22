<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\app_contract\Contract\Blog\ArticleRepository;
use Drupal\app_contract\Contract\Node\Article as ArticleNode;
use Drupal\app_contract\Contract\Tag\TagRepository;
use Drupal\niklan\ExternalContent\Domain\Article;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslation;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleProcessPipeline;
use Drupal\node\NodeStorageInterface;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\SyncContext>
 */
final readonly class ArticleProcessor implements PipelineStage {

  public function __construct(
    private TagRepository $tagRepository,
    private ArticleRepository $articleRepository,
    private EntityTypeManagerInterface $entityTypeManager,
    private ArticleProcessPipeline $pipeline,
  ) {}

  public function process(PipelineContext $context): void {
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
    if ($this->shouldSkipUpdate($article, $article_entity, $context)) {
      $context->getLogger()->info('Skipping update, article not changed', [
        'article_id' => $article->id,
      ]);
      return;
    }

    $this->updateArticleMetadata($article, $article_entity);
    $this->processTranslations($article, $article_entity, $context);

    $article_entity->save();
  }

  private function processTranslations(Article $article, ArticleNode $entity, SyncContext $context): void {
    $this->processPrimaryTranslation($article, $entity, $context);
    $this->processAdditionalTranslations($article, $entity, $context);
  }

  private function processPrimaryTranslation(Article $article, ArticleNode $entity, SyncContext $context): void {
    $primary_translation = $article->getPrimaryTranslation();
    $this->processArticleTranslation($article, $primary_translation, $entity, $context);
  }

  private function processAdditionalTranslations(Article $article, ArticleNode $entity, SyncContext $context): void {
    if (!$entity->isTranslatable()) {
      return;
    }

    foreach ($article->getTranslations() as $translation) {
      if ($translation->isPrimary) {
        continue;
      }
      $this->processArticleTranslation($article, $translation, $entity, $context);
    }
  }

  private function shouldSkipUpdate(Article $article, ArticleNode $article_entity, SyncContext $context): bool {
    if ($context->isForced() || $article_entity->isNew()) {
      return FALSE;
    }

    $article_updated = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC')->getTimestamp();
    return $article_entity->getChangedTime() >= $article_updated;
  }

  private function processArticleTranslation(Article $article, ArticleTranslation $translation, ArticleNode $article_entity, SyncContext $context): void {
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
    return $this->entityTypeManager->getStorage('node');
  }

  private function findOrCreateArticleEntity(Article $article): ArticleNode {
    $article_entity = $this->articleRepository->findByExternalId($article->id)
      ?? $this->getBlogStorage()->create(['type' => 'blog_entry']);
    \assert($article_entity instanceof ArticleNode);

    if ($article_entity->isNew()) {
      $article_entity->setExternalId($article->id);
      $article_entity->setOwnerId(1);
    }

    return $article_entity;
  }

  private function updateArticleMetadata(Article $article, ArticleNode $article_entity): void {
    $created_date = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->created, 'UTC');
    $article_entity->setCreatedTime($created_date->getTimestamp());

    $updated_date = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC');
    $article_entity->setChangedTime($updated_date->getTimestamp());

    $this->updateTags($article, $article_entity);
  }

  private function updateTags(Article $article, ArticleNode $article_entity): void {
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
