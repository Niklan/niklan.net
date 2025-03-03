<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Psr\Log\LoggerInterface;

final readonly class BlogSyncPipeline implements Pipeline {

  private SequentialPipeline $pipeline;

  public function __construct(
    private LoggerInterface $logger,
  ) {
    $this->pipeline = new SequentialPipeline($logger);
  }

  public function addStage(Stage $stage, ?Config $config = NULL): void {
    $this->pipeline->addStage($stage, $config);
  }

  public function run(Context $context): Context {
    \assert($context instanceof BlogSyncContext);

    return $this->pipeline->run($context);
  }

}
