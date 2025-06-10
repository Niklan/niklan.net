<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Stages\ArticleTranslationFieldUpdater;
use Drupal\niklan\ExternalContent\Stages\AssetSynchronizer;
use Drupal\niklan\ExternalContent\Stages\MarkdownToAstParser;

final readonly class ArticleProcessPipeline implements Pipeline {

  private Pipeline $pipeline;

  public function __construct() {
    $this->pipeline = new SequentialPipeline();
    $this->pipeline->addStage(new MarkdownToAstParser());
    $this->pipeline->addStage(new AssetSynchronizer());
    $this->pipeline->addStage(new ArticleTranslationFieldUpdater());
  }

  public function addStage(PipelineStage $stage, int $priority = 0): void {
    $this->pipeline->addStage($stage, $priority);
  }

  /**
   * @param \Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext $context
   */
  public function run(PipelineContext $context): void {
    $this->pipeline->run($context);
  }

}
