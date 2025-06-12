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
  }

}
