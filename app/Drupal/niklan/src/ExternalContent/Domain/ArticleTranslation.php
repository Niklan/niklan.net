<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

final class ArticleTranslation {

  /**
   * @var array{}|list<array{src: string, title: string}>
   */
  private array $attachments = [];

  public function __construct(
    public readonly string $sourcePath,
    public readonly string $language,
    public readonly string $title,
    public readonly string $description,
    public readonly string $posterPath,
    public readonly bool $isPrimary = FALSE,
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
