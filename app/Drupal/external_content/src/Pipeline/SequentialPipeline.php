<?php

declare(strict_types=1);

namespace Drupal\external_content\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Utils\PrioritizedList;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\Pipeline<\Drupal\external_content\Contract\Pipeline\PipelineStage, \Drupal\external_content\Contract\Pipeline\PipelineContext>
 */
final class SequentialPipeline implements Pipeline {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Pipeline\PipelineStage>
   */
  private PrioritizedList $stages;

  public function __construct() {
    $this->stages = new PrioritizedList();
  }

  public function addStage(PipelineStage $stage, int $priority = 0): void {
    $this->stages->add($stage, $priority);
  }

  public function run(PipelineContext $context): void {
    foreach ($this->stages as $stage) {
      try {
        $stage->process($context);
      }
      catch (\Throwable $e) {
        $context->getLogger()->error('Stage {stage} failed: {message}', [
          'stage' => $stage::class,
          'message' => $e->getMessage(),
          'exception' => $e,
        ]);
      }
    }
  }

}
