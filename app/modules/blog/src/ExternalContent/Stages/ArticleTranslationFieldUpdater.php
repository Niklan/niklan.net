<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\app_blog\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\app_blog\Plugin\ExternalContent\Environment\BlogArticle;
use Drupal\app_contract\Utils\PathHelper;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\app_blog\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
final readonly class ArticleTranslationFieldUpdater implements PipelineStage {

  /**
   * MD5 hash of the source file path relative to the working directory.
   *
   * Using a relative path (instead of absolute) ensures the hash is stable
   * across environments (production, local dev, etc.) where the absolute mount
   * points differ.
   *
   * @see self::syncExternalContent()
   * @see \Drupal\app_blog\ExternalContent\Stages\LinkProcessor::markAsInternalArticleLink()
   */
  public const string SOURCE_PATH_HASH_PROPERTY = 'source_path_hash';

  public function __construct(
    private EnvironmentManager $environmentManager,
  ) {}

  public function process(PipelineContext $context): void {
    \assert($context->externalContent instanceof Document);
    $this->syncTitle($context);
    $this->syncDescription($context);
    $this->syncExternalContent($context);
    $this->syncAttachments($context);
    $this->syncPoster($context);
  }

  private function syncTitle(ArticleTranslationProcessContext $context): void {
    $context->articleEntity->setTitle($context->articleTranslation->title);
  }

  private function syncPoster(ArticleTranslationProcessContext $context): void {
    $context->articleEntity->set('field_media_image', $context->posterMedia);
  }

  private function syncAttachments(ArticleTranslationProcessContext $context): void {
    $context->articleEntity->set('field_media_attachments', NULL);
    foreach ($context->attachmentsMedia as $attachment_media) {
      $context->articleEntity->get('field_media_attachments')->appendItem($attachment_media);
    }
  }

  private function syncDescription(ArticleTranslationProcessContext $context): void {
    $context->articleEntity->set('body', $context->articleTranslation->description);
  }

  private function syncExternalContent(ArticleTranslationProcessContext $context): void {
    \assert($context->externalContent instanceof Document);
    $environment = $this->environmentManager->createInstance(BlogArticle::ID);
    \assert($environment instanceof BlogArticle);

    $source_path = $context->articleTranslation->contentDirectory . \DIRECTORY_SEPARATOR . $context->articleTranslation->sourcePath;
    $context->articleEntity->set('external_content', [
      'value' => $environment->normalize($context->externalContent),
      'environment_id' => BlogArticle::ID,
      'data' => \json_encode([
        self::SOURCE_PATH_HASH_PROPERTY => PathHelper::hashRelativePath(
          path: $source_path,
          base_path: $context->syncContext->contentRoot,
        ),
      ]),
    ]);
  }

}
