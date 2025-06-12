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

      // @todo https://github.com/Niklan/niklan.net/blob/main/app/Drupal/external_content/src/Source/File.php#L74
      $article = $this->articleParser->parse($file->getPathname());
      $context->addArticle($article);
    }
  }

}
