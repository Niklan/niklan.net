<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleProcessPipeline;
use Drupal\niklan\Node\Entity\BlogEntryInterface;

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
      $context->getLogger()->info('Processing article', ['article' => $article]);
      $article_entity = $this->findOrCreateArticleEntity();
      $translation = $article->getPrimaryTranslation();
      $context->getLogger()->info('Processing translation', ['translation' => $translation]);
      $article_process_context = new ArticleTranslationProcessContext($article, $translation, $article_entity, $context);
      $this->pipeline->run($article_process_context);
      foreach ($article->getTranslations() as $translation) {
        if ($translation->isPrimary) {
          continue;
        }
        $context->getLogger()->info('Processing translation', ['translation' => $translation]);
        $article_process_context = new ArticleTranslationProcessContext($article, $translation, $article_entity, $context);
        $this->pipeline->run($article_process_context);
      }
      // @todo upadteArticleMetadata.
      // @todo Save node.
    }
  }

  private function findOrCreateArticleEntity(): BlogEntryInterface {
    // @todo Complete it.
    return \Drupal::entityTypeManager()->getStorage('node')->create(['type' => 'blog_entry']);
  }

}
