<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;

final readonly class ArticleTranslationFieldUpdater implements PipelineStage {

  public function process(PipelineContext $context): void {
    // @todo Use DI.
    $environment_manager = \Drupal::service(EnvironmentManager::class);
    $environment = $environment_manager->createInstance(BlogArticle::ID);
    \assert($environment instanceof BlogArticle);
    \assert($context instanceof ArticleTranslationProcessContext);
    // dump($environment->normalize($context->ast));.
    $environment->denormalize($environment->normalize($context->ast));
    // Set to data links MD5
    // https://github.com/Niklan/niklan.net/blob/8f05155e0ec7a5833368f1ead52d7b3e68753a4f/app/Drupal/niklan/src/ExternalContent/Loader/Blog.php#L325-L329
  }

}
