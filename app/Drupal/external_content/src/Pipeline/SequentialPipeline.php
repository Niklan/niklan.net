<?php

declare(strict_types=1);

namespace Drupal\external_content\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineConfig;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Utils\PrioritizedList;

final class SequentialPipeline implements Pipeline {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<array{stage: \Drupal\external_content\Contract\Pipeline\PipelineStage, config: \Drupal\external_content\Contract\Pipeline\PipelineConfig}>
   */
  private PrioritizedList $stages;

  public function __construct() {
    $this->stages = new PrioritizedList();
  }

  public function addStage(PipelineStage $stage, PipelineConfig $config = new NullPipelineConfig()): void {
    $this->stages->add([
      'stage' => $stage,
      'config' => $config,
    ], 0);
  }

  public function run(PipelineContext $context): void {
    foreach ($this->stages as $stage_data) {
      try {
        $stage_data['stage']->process($context, $stage_data['config']);
      }
      catch (\Throwable $e) {
        $context->getLogger()->error('Stage failed: ' . $e->getMessage());
      }
    }
  }

}
