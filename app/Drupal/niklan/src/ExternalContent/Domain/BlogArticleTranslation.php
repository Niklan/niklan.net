<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

final readonly class BlogArticleTranslation {

  /**
   * @param non-empty-string $sourcePath
   * @param non-empty-string $language
   * @param non-empty-string $title
   * @param non-empty-string $description
   * @param non-empty-string $posterPath
   * @param bool $isPrimary
   */
  public function __construct(
    public string $sourcePath,
    public string $language,
    public string $title,
    public string $description,
    public string $posterPath,
    public bool $isPrimary = FALSE,
  ) {}

}
