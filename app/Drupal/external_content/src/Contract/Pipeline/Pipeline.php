<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

/**
 * @template TStage = \Drupal\external_content\Contract\Pipeline\PipelineStage
 * @template TContext = \Drupal\external_content\Contract\Pipeline\PipelineContext
 */
interface Pipeline {

  /**
   * @param TStage<TContext> $stage
   */
  public function addStage(PipelineStage $stage, int $priority = 0): void;

  /**
   * @param TContext $context
   */
  public function run(PipelineContext $context): void;

}
