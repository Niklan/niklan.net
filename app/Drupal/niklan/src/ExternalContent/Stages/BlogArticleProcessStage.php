<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Pipeline\BlogArticleProcessPipeline;

final readonly class BlogArticleProcessStage implements Stage {

  public function __construct(
    private BlogArticleProcessPipeline $pipeline,
  ) {}

  public function process(Context $context, Config $config): Context {
    \assert($context instanceof BlogSyncContext);

    return $this->pipeline->run($context);
  }

}
