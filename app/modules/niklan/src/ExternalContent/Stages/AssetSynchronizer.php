<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Nodes\Node;
use Drupal\media\MediaInterface;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Nodes\Image\Image;
use Drupal\niklan\ExternalContent\Nodes\LocalVideo\LocalVideo;
use Drupal\niklan\ExternalContent\Nodes\MediaReference\MediaReference;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\RemoteVideo;
use Drupal\niklan\Media\Contract\MediaSynchronizer;

/**
 * @implements \Drupal\external_content\Contract\Pipeline\PipelineStage<\Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext>
 */
final readonly class AssetSynchronizer implements PipelineStage {

  public function __construct(
    private MediaSynchronizer $mediaSynchronizer,
  ) {}

  public function process(PipelineContext $context): void {
    \assert($context->externalContent instanceof Node);
    $this->syncExternalContentRecursively($context->externalContent, $context);
    $this->syncPoster($context);
    $this->syncAttachments($context);
  }

  private function syncPoster(ArticleTranslationProcessContext $context): void {
    $asset_path = $context->articleTranslation->contentDirectory . '/' . $context->articleTranslation->posterPath;
    $context->posterMedia = $this->mediaSynchronizer->sync($asset_path);
  }

  private function syncAttachments(ArticleTranslationProcessContext $context): void {
    foreach ($context->articleTranslation->getAttachments() as $attachment) {
      $asset_path = $context->articleTranslation->contentDirectory . '/' . $attachment['src'];
      $attachment_media = $this->mediaSynchronizer->sync($asset_path, ['title' => $attachment['title']]);
      if (!($attachment_media instanceof MediaInterface)) {
        continue;
      }

      $context->attachmentsMedia[] = $attachment_media;
    }
  }

  private function syncExternalContentRecursively(Node $node, ArticleTranslationProcessContext $context): void {
    foreach ($node->getChildren() as $child) {
      $this->syncExternalContentRecursively($child, $context);
    }
    $this->syncExternalContentNode($node, $context);
  }

  private function syncExternalContentNode(Node $node, ArticleTranslationProcessContext $context): void {
    match ($node::class) {
      Image::class => $this->syncImage($node, $context),
      LocalVideo::class => $this->syncLocalVideo($node, $context),
      RemoteVideo::class => $this->syncRemoteVideo($node, $context),
      default => NULL,
    };
  }

  private function replaceWithMediaReferenceNode(Node $original_node, string $asset_source, array $media_metadata = []): void {
    $media = $this->mediaSynchronizer->sync($asset_source);
    if ($media?->uuid()) {
      $media_node = new MediaReference($media->uuid(), $media_metadata);
      $original_node->getParent()->replaceChild($original_node, $media_node);
    }
    else {
      $original_node->getParent()->removeChild($original_node);
    }
  }

  private function syncImage(Image $node, ArticleTranslationProcessContext $context): void {
    $asset_path = $context->articleTranslation->contentDirectory . '/' . $node->src;
    $data = [
      'src' => $node->src,
      'alt' => $node->alt,
    ];
    $this->replaceWithMediaReferenceNode($node, $asset_path, $data);
  }

  private function syncLocalVideo(LocalVideo $node, ArticleTranslationProcessContext $context): void {
    $asset_path = $context->articleTranslation->contentDirectory . '/' . $node->src;
    $data = ['title' => $node->title];
    $this->replaceWithMediaReferenceNode($node, $asset_path, $data);
  }

  private function syncRemoteVideo(RemoteVideo $node, ArticleTranslationProcessContext $context): void {
    $this->replaceWithMediaReferenceNode($node, $node->url);
  }

}
