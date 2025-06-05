<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleProcessPipeline;

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
      // @todo Find/create node.
      // @todo Process primary translation.
      foreach ($article->getTranslations() as $translation) {
        if ($translation->isPrimary) {
          continue;
        }
        // @todo Process other translations.
      }
      $article_process_context = new ArticleTranslationProcessContext($article, $context);
      $this->pipeline->run($article_process_context);
      // @todo upadteArticleMetadata.
      // @todo Save node.
    }
  }

}
