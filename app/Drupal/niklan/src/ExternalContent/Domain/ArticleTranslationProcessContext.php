<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Nodes\RootNode;
use Drupal\media\MediaInterface;
use Drupal\niklan\Node\Entity\BlogEntryInterface;
use Psr\Log\LoggerInterface;

final class ArticleTranslationProcessContext implements PipelineContext {

  /**
   * @param array<int,\Drupal\media\MediaInterface>|null $attachmentsMedia
   */
  public function __construct(
    public readonly Article $article,
    public readonly ArticleTranslation $articleTranslation,
    public readonly BlogEntryInterface $articleEntity,
    public readonly SyncContext $syncContext,
    public ?RootNode $externalContent = NULL,
    public ?MediaInterface $promoImageMeda = NULL,
    public ?array $attachmentsMedia = NULL,
  ) {}

  #[\Override]
  public function getLogger(): LoggerInterface {
    return $this->syncContext->getLogger();
  }

  public function isStrictMode(): bool {
    return $this->syncContext->isStrictMode();
  }

}
