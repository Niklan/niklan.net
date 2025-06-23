<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;
use Drupal\niklan\Utils\PathHelper;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
final readonly class ArticleTranslationFieldUpdater implements PipelineStage {

  private EnvironmentManager $environmentManager;

  public function __construct() {
    // @todo Add proper DI.
    $this->environmentManager = \Drupal::service(EnvironmentManager::class);
  }

  public function process(PipelineContext $context): void {
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
    $environment = $this->environmentManager->createInstance(BlogArticle::ID);
    \assert($environment instanceof BlogArticle);
    $source_path = PathHelper::normalizePath($context->articleTranslation->contentDirectory . '/' . $context->articleTranslation->sourcePath);
    $context->articleEntity->set('external_content', [
      'value' => $environment->normalize($context->externalContent),
      'environment_id' => BlogArticle::ID,
      'data' => \json_encode([
        // MD5 simply for less data to store.
        'source_path_hash' => \md5($source_path),
      ]),
    ]);
  }

}
