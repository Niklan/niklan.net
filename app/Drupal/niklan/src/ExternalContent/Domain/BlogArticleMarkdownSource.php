<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Importer\ImporterSource;

/**
 * @implements \Drupal\external_content\Contract\Importer\ImporterSource<string>
 */
final readonly class BlogArticleMarkdownSource implements ImporterSource {

  public function __construct(
    private string $markdown,
  ) {}

  public function getSourceData(): string {
    return $this->markdown;
  }

}
