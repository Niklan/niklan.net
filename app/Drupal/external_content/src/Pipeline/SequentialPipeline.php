<?php

declare(strict_types=1);

namespace Drupal\external_content\Pipeline;

use Drupal\external_content\Contract\Exception\PipelineException;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Utils\PrioritizedList;
use Psr\Log\LogLevel;

/**
 * @template TStage of \Drupal\external_content\Contract\Pipeline\PipelineStage
 * @template TContext of \Drupal\external_content\Contract\Pipeline\PipelineContext
 * @implements \Drupal\external_content\Contract\Pipeline\Pipeline<TStage, TContext>
 */
final class SequentialPipeline implements Pipeline {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<TStage>
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
      catch (PipelineException $exception) {
        $this->handleException($context, $exception, $stage, LogLevel::ERROR);
      }
      catch (\Throwable $exception) {
        $this->handleException($context, $exception, $stage, LogLevel::CRITICAL);
      }
    }
  }

  private function handleException(PipelineContext $context, \Throwable $exception, PipelineStage $stage, string $log_level): void {
    $this->logException($context, $exception, $stage, $log_level);
    if ($context->isStrictMode()) {
      throw $exception;
    }
  }

  private function logException(PipelineContext $context, \Throwable $exception, PipelineStage $stage, string $log_level): void {
    $context->getLogger()->log($log_level, 'Stage processing failed', [
      'stage' => $stage::class,
      'message' => $exception->getMessage(),
      'exception' => $exception,
      'trace' => $exception->getTraceAsString(),
    ]);
  }

}
