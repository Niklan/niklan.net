<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Domain;

final readonly class ArticleProcessingContext {

  public function __construct(
    public ArticleTranslation $translation,
    public string $contentRoot,
  ) {}

}
