<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;

final readonly class BlogArticleProcessPipeline implements Pipeline {

  private Pipeline $pipeline;

  public function __construct() {
    $this->pipeline = new SequentialPipeline();
  }

  public function addStage(Stage $stage, ?Config $config = NULL): void {
    $this->pipeline->addStage($stage, $config);
  }

  public function run(Context $context): Context {
    if (!$context instanceof BlogSyncContext) {
      throw new \InvalidArgumentException('Invalid context');
    }

    return $this->pipeline->run($context);
  }

}
