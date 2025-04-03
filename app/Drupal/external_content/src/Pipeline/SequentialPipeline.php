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
      catch (PipelineException $exception) {
        $this->handleCheckedException($context, $exception, $stage);
      }
      catch (\Throwable $exception) {
        $this->handleUncheckedException($context, $exception, $stage);
      }
    }
  }

  private function handleCheckedException(PipelineContext $context, PipelineException $exception, PipelineStage $stage): void {
    $this->logException($context, $exception, $stage, LogLevel::ERROR);
    if ($context->isStrictMode()) {
      throw $exception;
    }
  }

  private function handleUncheckedException(PipelineContext $context, \Throwable $exception, PipelineStage $stage): void {
    $this->logException($context, $exception, $stage, LogLevel::CRITICAL);
  }

  private function logException(PipelineContext $context, \Throwable $exception, PipelineStage $stage, string $log_level): void {
    $context->getLogger()->log($log_level, 'Stage {stage} failed: {message}', [
      'stage' => $stage::class,
      'message' => $exception->getMessage(),
      'exception' => $exception,
    ]);
  }

}
