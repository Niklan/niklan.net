<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Domain\MarkdownSource;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;
use Drupal\niklan\Utils\PathHelper;

final readonly class MarkdownToAstParser implements PipelineStage {

  /**
   * @param \Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext $context
   */
  public function process(PipelineContext $context): void {
    // @todo Use DI.
    $environment_manager = \Drupal::service(EnvironmentManager::class);
    $environment = $environment_manager->createInstance(BlogArticle::ID);
    \assert($environment instanceof BlogArticle);

    \assert($context instanceof ArticleTranslationProcessContext);
    $markdown_file = PathHelper::normalizePath($context->article->directory . '/' . $context->article->getPrimaryTranslation()->sourcePath);

    $context->getLogger()->info('Markdown conversion started', [
      'file' => $markdown_file,
      'environment_id' => $environment->getPluginId(),
    ]);

    $source = new MarkdownSource(\file_get_contents($markdown_file));
    $context->ast = $environment->parse($source);
  }

}
