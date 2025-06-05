<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Nodes\RootNode;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;
use Psr\Log\LoggerInterface;

final class ArticleTranslationProcessContext implements PipelineContext {

  public function __construct(
    public readonly Article $article,
    public readonly ArticleTranslation $translation,
    public readonly SyncContext $syncContext,
    public readonly BlogArticle $blogArticle,
    public readonly ?string $html = NULL,
    public readonly ?RootNode $ast = NULL,
  ) {}

  #[\Override]
  public function getLogger(): LoggerInterface {
    return $this->syncContext->getLogger();
  }

  public function isStrictMode(): bool {
    return $this->syncContext->isStrictMode();
  }

}
