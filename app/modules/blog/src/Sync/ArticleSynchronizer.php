<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\app_blog\Sync\Domain\Article;
use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\SyncContext;
use Drupal\app_blog\Sync\Parser\ArticleXmlParser;
use Drupal\app_contract\Contract\Blog\ArticleRepository;
use Drupal\app_contract\Contract\Node\Article as ArticleNode;
use Drupal\app_contract\Contract\Tag\TagRepository;
use Symfony\Component\Finder\Finder;

final readonly class ArticleSynchronizer {

  public function __construct(
    private ArticleXmlParser $articleParser,
    private ArticleProcessor $articleProcessor,
    private ArticleMapper $articleMapper,
    private ArticleRepository $articleRepository,
    private TagRepository $tagRepository,
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function sync(SyncContext $context): void {
    $articles = $this->findArticles($context);

    foreach ($articles as $article) {
      $this->syncArticle($article, $context);
    }
  }

  /**
   * @return list<\Drupal\app_blog\Sync\Domain\Article>
   */
  private function findArticles(SyncContext $context): array {
    $context->logger->info('Blog article search initiated', [
      'working_directory' => $context->workingDirectory,
    ]);

    $finder = new Finder();
    $finder->in($context->workingDirectory)->name('article.xml');

    $articles = [];
    foreach ($finder as $file) {
      if ($file->isDir()) {
        continue;
      }

      $articles[] = $this->articleParser->parse($file->getPathname());
    }

    $context->logger->info('Blog articles found', ['count' => \count($articles)]);

    return $articles;
  }

  private function syncArticle(Article $article, SyncContext $context): void {
    $context->logger->info('Processing article', ['article_id' => $article->id]);

    try {
      $entity = $this->findOrCreateEntity($article);
      if ($this->shouldSkipUpdate($article, $entity, $context)) {
        $context->logger->info('Skipping, article not changed', ['article_id' => $article->id]);
        return;
      }

      $this->updateMetadata($article, $entity);
      $this->processTranslations($article, $entity, $context);
      $entity->save();
    }
    catch (\Throwable $exception) {
      $context->logger->error('Failed to sync article', [
        'article_id' => $article->id,
        'error' => $exception->getMessage(),
      ]);
    }
  }

  private function processTranslations(Article $article, ArticleNode $entity, SyncContext $context): void {
    foreach ($article->getTranslations() as $translation) {
      $this->processTranslation($translation, $entity, $context);
    }
  }

  private function processTranslation(ArticleTranslation $translation, ArticleNode $entity, SyncContext $context): void {
    $context->logger->info('Processing translation', ['language' => $translation->language]);

    $entity_translation = $this->resolveTranslation($translation, $entity);
    $processed = $this->articleProcessor->process($translation, $context->contentRoot);
    $this->articleMapper->toEntity($processed, $entity_translation);
  }

  private function resolveTranslation(ArticleTranslation $translation, ArticleNode $entity): ArticleNode {
    if ($translation->isPrimary) {
      return $entity;
    }

    return $entity->hasTranslation($translation->language)
      ? $entity->getTranslation($translation->language)
      : $entity->addTranslation($translation->language);
  }

  private function findOrCreateEntity(Article $article): ArticleNode {
    $entity = $this->articleRepository->findByExternalId($article->id)
      ?? $this->entityTypeManager->getStorage('node')->create(['type' => 'blog_entry']);
    \assert($entity instanceof ArticleNode);

    if ($entity->isNew()) {
      $entity->setExternalId($article->id);
      $entity->setOwnerId(1);
    }

    return $entity;
  }

  private function shouldSkipUpdate(Article $article, ArticleNode $entity, SyncContext $context): bool {
    if ($context->isForced() || $entity->isNew()) {
      return FALSE;
    }

    $updated = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC')->getTimestamp();
    return $entity->getChangedTime() >= $updated;
  }

  private function updateMetadata(Article $article, ArticleNode $entity): void {
    $created = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->created, 'UTC');
    $entity->setCreatedTime($created->getTimestamp());

    $updated = DrupalDateTime::createFromFormat('Y-m-d\TH:i:s', $article->updated, 'UTC');
    $entity->setChangedTime($updated->getTimestamp());

    $this->updateTags($article, $entity);
  }

  private function updateTags(Article $article, ArticleNode $entity): void {
    $entity->set('field_tags', NULL);
    foreach ($article->tags as $tag) {
      $tag_entity = $this->tagRepository->findByExternalId($tag);
      if (!$tag_entity) {
        continue;
      }

      $entity->get('field_tags')->appendItem($tag_entity->id());
    }
  }

}
