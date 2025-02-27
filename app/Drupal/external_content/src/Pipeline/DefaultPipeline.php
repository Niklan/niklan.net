<?php

declare(strict_types=1);

namespace Drupal\external_content\Pipeline;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\Stage;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

final class DefaultPipeline implements Pipeline {

  /**
   * @var list<array{
   *   stage: \Drupal\external_content\Contract\Pipeline\Stage,
   *   config: \Drupal\external_content\Contract\Pipeline\Config
   *  }>
   */
  private array $stages = [];

  public function __construct(
    private readonly LoggerInterface $logger,
  ) {}

  public function addStage(Stage $stage, ?Config $config = NULL): void {
    if ($stage instanceof LoggerAwareInterface) {
      $stage->setLogger($this->logger);
    }

    $this->stages[] = [
      'stage' => $stage,
      'config' => $config ?? new NullConfig(),
    ];
  }

  public function run(Context $context): Context {
    $current_context = $context;

    foreach ($this->stages as $stage_data) {
      $current_context = $stage_data['stage']->process($current_context, $stage_data['config']);
    }

    return $current_context;
  }

}
