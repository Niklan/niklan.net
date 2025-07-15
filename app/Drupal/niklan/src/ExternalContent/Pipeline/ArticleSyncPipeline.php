<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Stages\ArticleFinder;
use Drupal\niklan\ExternalContent\Stages\ArticleProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\Pipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\SyncContext>, \Drupal\niklan\ExternalContent\Domain\SyncContext>
 */
#[Autoconfigure(
  calls: [
    ['addStage', [ArticleFinder::class]],
    ['addStage', [ArticleProcessor::class]],
  ],
)]
final readonly class ArticleSyncPipeline implements Pipeline {

  /**
   * @var \Drupal\external_content\Pipeline\SequentialPipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\SyncContext>, \Drupal\niklan\ExternalContent\Domain\SyncContext>
   */
  private SequentialPipeline $pipeline;

  public function __construct() {
    $this->pipeline = new SequentialPipeline();
  }

  public function addStage(PipelineStage $stage, int $priority = 0): void {
    $this->pipeline->addStage($stage, $priority);
  }

  public function run(PipelineContext $context): void {
    $this->pipeline->run($context);
  }

}
