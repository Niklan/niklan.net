<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

use Drupal\external_content\Pipeline\NullPipelineConfig;

interface Pipeline {

  public function addStage(PipelineStage $stage, PipelineConfig $config = new NullPipelineConfig()): void;

  public function run(PipelineContext $context): void;

}
