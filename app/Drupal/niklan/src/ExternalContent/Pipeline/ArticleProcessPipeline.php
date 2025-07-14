<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Stages\ArticleTranslationFieldUpdater;
use Drupal\niklan\ExternalContent\Stages\AssetSynchronizer;
use Drupal\niklan\ExternalContent\Stages\LinkProcessor;
use Drupal\niklan\ExternalContent\Stages\MarkdownToAstParser;

final readonly class ArticleProcessPipeline implements Pipeline {

  private Pipeline $pipeline;

  public function __construct() {
    $this->pipeline = new SequentialPipeline();
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
