<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\Node\Entity\Node;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;
use Drupal\niklan\Utils\PathHelper;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
final readonly class ArticleTranslationFieldUpdater implements PipelineStage {

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

    $source_path = PathHelper::normalizePath($context->articleTranslation->contentDirectory . \DIRECTORY_SEPARATOR . $context->articleTranslation->sourcePath);
    $context->articleEntity->set('external_content', [
      'value' => $environment->normalize($context->externalContent),
      'environment_id' => BlogArticle::ID,
      'data' => \json_encode([
        // MD5 simply for less data to store.
        self::SOURCE_PATH_HASH_PROPERTY => \md5($source_path),
      ]),
    ]);
  }

}
