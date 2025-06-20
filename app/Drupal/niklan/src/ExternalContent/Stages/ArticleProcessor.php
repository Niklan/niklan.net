<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\Article;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslation;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleProcessPipeline;
use Drupal\niklan\Node\Entity\BlogEntryInterface;
use Drupal\node\NodeStorageInterface;

final readonly class ArticleProcessor implements PipelineStage {

  private Pipeline $pipeline;

  public function __construct() {
    $this->pipeline = new ArticleProcessPipeline();
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
    $this->updateArticleMetadata($article, $article_entity);
    $translation = $article->getPrimaryTranslation();
    $this->processArticleTranslation($article, $translation, $article_entity, $context);

    foreach ($article->getTranslations() as $translation) {
      if ($translation->isPrimary) {
        continue;
      }
      $this->processArticleTranslation($article, $translation, $article_entity, $context);
    }
  }

  private function processArticleTranslation(Article $article, ArticleTranslation $translation, BlogEntryInterface $article_entity, SyncContext $context): void {
    $context->getLogger()->info('Processing article translation', [
      'article_id' => $article->id,
      'language' => $translation->language,
      'source_file' => $translation->sourcePath,
    ]);

    $article_process_context = new ArticleTranslationProcessContext($article, $translation, $article_entity, $context);
    $this->pipeline->run($article_process_context);
  }

  private function getBlogStorage(): NodeStorageInterface {
    // @todo Replace by DI.
    return \Drupal::entityTypeManager()->getStorage('node');
  }

  private function findOrCreateArticleEntity(Article $article): BlogEntryInterface {
    // @phpstan-ignore-next-line
    return $this->findExistingEntity($article) ?? $this->getBlogStorage()->create([
      'type' => 'blog_entry',
      'external_id' => $article->id,
    ]);
  }

  private function findExistingEntity(Article $article): ?BlogEntryInterface {
    $ids = $this
      ->getBlogStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('external_id', $article->id)
      ->range(0, 1)
      ->execute();
    // @phpstan-ignore-next-line
    return $ids ? $this->getBlogStorage()->load(\reset($ids)) : NULL;
  }

  private function updateArticleMetadata(Article $article, BlogEntryInterface $article_entity): void {
    $created_date = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->created, 'UTC');
    $article_entity->setCreatedTime($created_date->getTimestamp());

    $updated_date = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC');
    $article_entity->setChangedTime($updated_date->getTimestamp());

    // @todo Sync tags.
  }

}
