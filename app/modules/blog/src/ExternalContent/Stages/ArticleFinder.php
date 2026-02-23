<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\app_blog\ExternalContent\Parser\ArticleXmlParser;
use Symfony\Component\Finder\Finder;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\app_blog\ExternalContent\Domain\SyncContext>
 */
final readonly class ArticleFinder implements PipelineStage {

  public function __construct(
    private ArticleXmlParser $articleParser,
  ) {}

  /**
   * @param \Drupal\app_blog\ExternalContent\Domain\SyncContext $context
   */
  public function process(PipelineContext $context): void {
    $pattern = 'article.xml';
    $context->getLogger()->info('Blog article search initiated', [
      'working_directory' => $context->workingDirectory,
      'search_pattern' => $pattern,
    ]);

    $finder = new Finder();
    $finder->in($context->workingDirectory);
    $finder->name($pattern);

    $context->getLogger()->info('Blog articles search completed', [
      'count' => $finder->count(),
    ]);

    foreach ($finder as $file) {
      if ($file->isDir()) {
        continue;
      }
      $article = $this->articleParser->parse($file->getPathname());
      $context->addArticle($article);
    }
  }

}
