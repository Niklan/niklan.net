<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Parser\ArticleXmlParser;
use Symfony\Component\Finder\Finder;

final readonly class ArticleFinder implements PipelineStage {

  public function __construct(
    private ArticleXmlParser $articleParser,
  ) {}

  /**
   * @param \Drupal\niklan\ExternalContent\Domain\SyncContext $context
   */
  public function process(PipelineContext $context): void {
    \assert($context instanceof SyncContext);

    $context->getLogger()->info(\sprintf('Looking for blog articles in %s', $context->workingDirectory));
    $finder = new Finder();
    $finder->in($context->workingDirectory);
    $finder->name('article.xml');
    $context->getLogger()->info(\sprintf('Found %s article sources', $finder->count()));

    foreach ($finder as $file) {
      if ($file->isDir()) {
        continue;
      }

      // @todo https://github.com/Niklan/niklan.net/blob/main/app/Drupal/external_content/src/Source/File.php#L74
      $article = $this->articleParser->parse($file->getPathname());
      $context->addArticle($article);
    }
  }

}
