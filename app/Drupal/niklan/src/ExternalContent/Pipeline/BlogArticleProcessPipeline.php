<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineConfig;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\NullPipelineConfig;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Domain\BlogArticleProcessPipelineContext;
use Drupal\niklan\ExternalContent\Domain\BlogSyncPipelineContext;

final readonly class BlogArticleProcessPipeline implements Pipeline {

  private Pipeline $pipeline;

  public function __construct() {
    $this->pipeline = new SequentialPipeline();
  }

  public function addStage(PipelineStage $stage, PipelineConfig $config = new NullPipelineConfig()): void {
    $this->pipeline->addStage($stage, $config);
  }

  /**
   * @param \Drupal\niklan\ExternalContent\Domain\BlogSyncPipelineContext $context
   */
  public function run(PipelineContext $context): void {
    if (!$context instanceof BlogSyncPipelineContext) {
      throw new \InvalidArgumentException('Invalid context');
    }

    foreach ($context->getArticles() as $article) {
      $article_process_context = new BlogArticleProcessPipelineContext($article, $context);
      $this->pipeline->run($article_process_context);
    }
  }

}
