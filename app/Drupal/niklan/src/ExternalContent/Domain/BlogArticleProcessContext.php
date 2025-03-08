<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\Context;
use Psr\Log\LoggerInterface;

final class BlogArticleProcessContext implements Context {

  public function __construct(
    public readonly BlogArticle $article,
    public readonly BlogSyncContext $syncContext,
  ) {}

  #[\Override]
  public function getLogger(): LoggerInterface {
    return $this->syncContext->getLogger();
  }

}
