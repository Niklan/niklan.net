<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Nodes\Document;
use Drupal\media\MediaInterface;
use Drupal\niklan\Node\Entity\BlogEntryInterface;
use Psr\Log\LoggerInterface;

final class ArticleTranslationProcessContext implements PipelineContext {

  /**
   * @param array<int,\Drupal\media\MediaInterface> $attachmentsMedia
   */
  public function __construct(
    public readonly Article $article,
    public readonly ArticleTranslation $articleTranslation,
    public readonly BlogEntryInterface $articleEntity,
    public readonly SyncContext $syncContext,
    public ?Document $externalContent = NULL,
    public ?MediaInterface $posterMedia = NULL,
    public array $attachmentsMedia = [],
  ) {}

  #[\Override]
  public function getLogger(): LoggerInterface {
    return $this->syncContext->getLogger();
  }

  public function isStrictMode(): bool {
    return $this->syncContext->isStrictMode();
  }

}
