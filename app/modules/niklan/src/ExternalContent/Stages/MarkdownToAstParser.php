<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;
use Drupal\niklan\Utils\PathHelper;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
final readonly class MarkdownToAstParser implements PipelineStage {

  public function __construct(
    private EnvironmentManager $environmentManager,
  ) {}

  /**
   * @param \Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext $context
   */
  public function process(PipelineContext $context): void {
    $environment = $this->environmentManager->createInstance(BlogArticle::ID);
    \assert($environment instanceof BlogArticle);
    $markdown_file = PathHelper::normalizePath($context->article->directory . '/' . $context->article->getPrimaryTranslation()->sourcePath);
    $context->getLogger()->info('Markdown conversion started', [
      'file' => $markdown_file,
      'environment_id' => $environment->getPluginId(),
    ]);

    $contents = \file_get_contents($markdown_file);
    \assert(\is_string($contents));
    $context->externalContent = $environment->parse($contents);
  }

}
