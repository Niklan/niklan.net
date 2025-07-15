<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

/**
 * @template T = \Drupal\external_content\Contract\Pipeline\PipelineContext
 */
interface PipelineStage {

  /**
   * @param T $context
   */
  public function process(PipelineContext $context): void;

}
