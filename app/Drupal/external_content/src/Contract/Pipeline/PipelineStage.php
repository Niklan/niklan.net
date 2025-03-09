<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

interface PipelineStage {

  public function process(PipelineContext $context, PipelineConfig $config): void;

}
