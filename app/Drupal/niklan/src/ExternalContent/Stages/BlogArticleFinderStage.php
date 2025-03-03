<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Infrastructure\ArticleParser;
use Symfony\Component\Finder\Finder;

final class BlogArticleFinderStage implements Stage {

  public function __construct(
    private readonly ArticleParser $articleParser,
  ) {}

  public function process(Context $context, Config $config): Context {
    \assert($context instanceof BlogSyncContext);

    $context->getLogger()->info(\sprintf('Looking for blog articles in %s', $context->workingDirectory));
    $finder = new Finder();
    $finder->in($context->workingDirectory);
    $finder->name('article.xml');
    $context->getLogger()->info(\sprintf('Found %s article sources', $finder->count()));

    foreach ($finder as $file) {
      if ($file->isDir()) {
        continue;
      }

      $article = $this->articleParser->parseFromFile($file->getPathname());
      $context->addArticle($article);
    }

    return $context;
  }

}
