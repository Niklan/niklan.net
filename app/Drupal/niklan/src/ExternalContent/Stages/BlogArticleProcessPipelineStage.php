<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineConfig;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\BlogSyncPipelineContext;
use Drupal\niklan\ExternalContent\Pipeline\BlogArticleProcessPipeline;

final readonly class BlogArticleProcessPipelineStage implements PipelineStage {

  public function __construct(
    private BlogArticleProcessPipeline $pipeline,
  ) {}

  public function process(PipelineContext $context, PipelineConfig $config): void {
    \assert($context instanceof BlogSyncPipelineContext);
    $this->pipeline->run($context);
  }

}
