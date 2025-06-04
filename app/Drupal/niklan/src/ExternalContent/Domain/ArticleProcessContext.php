<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Nodes\RootNode;
use Psr\Log\LoggerInterface;

final class ArticleProcessContext implements PipelineContext {

  public string $rawContent;
  public RootNode $astContent;

  public function __construct(
    public readonly Article $article,
    public readonly SyncContext $syncContext,
  ) {}

  #[\Override]
  public function getLogger(): LoggerInterface {
    return $this->syncContext->getLogger();
  }

  public function isStrictMode(): bool {
    return $this->syncContext->isStrictMode();
  }

}
