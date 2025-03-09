<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Psr\Log\LoggerInterface;

final class BlogArticleProcessPipelineContext implements PipelineContext {

  public function __construct(
    public readonly BlogArticle $article,
    public readonly BlogSyncPipelineContext $syncContext,
  ) {}

  #[\Override]
  public function getLogger(): LoggerInterface {
    return $this->syncContext->getLogger();
  }

}
