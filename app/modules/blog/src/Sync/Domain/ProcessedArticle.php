<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Domain;

use Drupal\media\MediaInterface;

final readonly class ProcessedArticle {

  /**
   * @param list<\Drupal\media\MediaInterface> $attachmentsMedia
   */
  public function __construct(
    public string $html,
    public string $sourcePathHash,
    public int $estimatedReadTime,
    public string $title,
    public string $description,
    public ?MediaInterface $posterMedia = NULL,
    public array $attachmentsMedia = [],
  ) {}

}
