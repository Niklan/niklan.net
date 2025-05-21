<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\Core\Site\Settings;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class BlogSyncPipelineContext implements PipelineContext {

  /**
   * @var array{}|list<\Drupal\niklan\ExternalContent\Domain\BlogArticle>
   */
  private array $articles = [];

  public function __construct(
    public readonly string $workingDirectory,
    public readonly LoggerInterface $logger = new NullLogger(),
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

  public function addArticle(BlogArticle $article): void {
    $this->articles[] = $article;
  }

  /**
   * @return array{}|list<\Drupal\niklan\ExternalContent\Domain\BlogArticle>
   */
  public function getArticles(): array {
    return $this->articles;
  }

  public function isStrictMode(): bool {
    return Settings::get('niklan_external_content_strict_mode', FALSE);
  }

}
