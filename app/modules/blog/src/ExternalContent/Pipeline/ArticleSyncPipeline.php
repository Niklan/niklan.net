<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\app_blog\ExternalContent\Stages\ArticleFinder;
use Drupal\app_blog\ExternalContent\Stages\ArticleProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\Pipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\app_blog\ExternalContent\Domain\SyncContext>, \Drupal\app_blog\ExternalContent\Domain\SyncContext>
 */
#[Autoconfigure(
  calls: [
    ['addStage', ['@' . ArticleFinder::class]],
    ['addStage', ['@' . ArticleProcessor::class]],
  ],
)]
final readonly class ArticleSyncPipeline implements Pipeline {

  /**
   * @var \Drupal\external_content\Pipeline\SequentialPipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\app_blog\ExternalContent\Domain\SyncContext>, \Drupal\app_blog\ExternalContent\Domain\SyncContext>
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
