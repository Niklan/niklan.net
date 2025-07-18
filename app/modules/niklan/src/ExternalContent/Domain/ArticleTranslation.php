<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

final class ArticleTranslation {

  public function __construct(
    public readonly string $sourcePath,
    public readonly string $language,
    public readonly string $title,
    public readonly string $description,
    public readonly string $posterPath,
    public readonly string $contentDirectory,
    public readonly bool $isPrimary = FALSE,
    private array $attachments = [],
  ) {}

  /**
   * @param array{src: string, title: string} $attachment
   */
  public function addAttachment(array $attachment): void {
    $this->attachments[] = $attachment;
  }

  /**
   * @return array{}|list<array{src: string, title: string}>
   */
  public function getAttachments(): array {
    return $this->attachments;
  }

}
