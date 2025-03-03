<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\external_content\Contract\Pipeline\Context;

final class BlogSyncContext implements Context {

  /**
   * @var array{}|list<\Drupal\niklan\ExternalContent\Domain\Drupal\niklan\ExternalContent\Domain\BlogArticle>
   */
  private array $articles = [];

  public function __construct(
    public readonly string $workingDirectory,
  ) {}

  public function addArticle(BlogArticle $article): void {
    $this->articles[] = $article;
  }

  /**
   * @return array{}|list<\Drupal\niklan\ExternalContent\Domain\Drupal\niklan\ExternalContent\Domain\BlogArticle>
   */
  public function getArticles(): array {
    return $this->articles;
  }

}
