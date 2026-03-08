<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Domain;

use Drupal\app_blog\ExternalContent\Domain\ArticleTranslation;

final readonly class ArticleProcessingContext {

  public function __construct(
    public ArticleTranslation $translation,
    public string $contentRoot,
  ) {}

}
