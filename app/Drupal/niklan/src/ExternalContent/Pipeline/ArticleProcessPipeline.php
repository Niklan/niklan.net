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
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\Pipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage, \Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
#[Autoconfigure(
  calls: [
    ['addStage', ['@' . MarkdownToAstParser::class]],
    ['addStage', ['@' . AssetSynchronizer::class]],
    ['addStage', ['@' . LinkProcessor::class]],
    ['addStage', ['@' . ArticleTranslationFieldUpdater::class]],
  ],
)]
final readonly class ArticleProcessPipeline implements Pipeline {

  /**
   * @var \Drupal\external_content\Pipeline\SequentialPipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>, \Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
   */
  private SequentialPipeline $pipeline;

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
