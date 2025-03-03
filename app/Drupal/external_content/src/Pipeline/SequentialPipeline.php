<?php

declare(strict_types=1);

namespace Drupal\external_content\Pipeline;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\Stage;

final class SequentialPipeline implements Pipeline {

  /**
   * @var list<array{
   *   stage: \Drupal\external_content\Contract\Pipeline\Stage,
   *   config: \Drupal\external_content\Contract\Pipeline\Config
   *  }>
   */
  private array $stages = [];

  public function addStage(Stage $stage, ?Config $config = NULL): void {
    $this->stages[] = [
      'stage' => $stage,
      'config' => $config ?? new NullConfig(),
    ];
  }

  public function run(Context $context): Context {
    $current_context = $context;

    foreach ($this->stages as $stage_data) {
      try {
        $current_context = $stage_data['stage']->process($current_context, $stage_data['config']);
      }
      catch (\Throwable $e) {
        $context->getLogger()->error('Stage failed: ' . $e->getMessage());
      }
    }

    return $current_context;
  }

}
