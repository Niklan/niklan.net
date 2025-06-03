<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleProcessPipeline;

final readonly class ArticleProcessor implements PipelineStage {

  public function __construct(
    private ArticleProcessPipeline $pipeline,
  ) {}

  public function process(PipelineContext $context): void {
    \assert($context instanceof SyncContext);
    $this->pipeline->run($context);
  }

}
