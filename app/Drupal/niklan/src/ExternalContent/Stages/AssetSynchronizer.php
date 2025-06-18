<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Nodes\ContentNode;
use Drupal\external_content\Nodes\Image\ImageNode;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\ExternalContent\Nodes\DrupalMedia\DrupalMediaNode;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\RemoteVideoNode;
use Drupal\niklan\ExternalContent\Nodes\Video\VideoNode;
use Drupal\niklan\Media\Contract\MediaSynchronizer;

final readonly class AssetSynchronizer implements PipelineStage {

  private MediaSynchronizer $mediaSynchronizer;

  public function __construct() {
    // @todo Use DI.
    $this->mediaSynchronizer = \Drupal::service(MediaSynchronizer::class);
  }

  public function process(PipelineContext $context): void {
    \assert($context instanceof ArticleTranslationProcessContext);
    $this->syncRecursively($context->ast, $context);
  }

  private function syncRecursively(ContentNode $node, ArticleTranslationProcessContext $context): void {
    foreach ($node->getChildren() as $child) {
      $this->syncRecursively($child, $context);
    }
    $this->syncSingle($node, $context);
  }

  private function syncSingle(ContentNode $node, ArticleTranslationProcessContext $context): void {
    match ($node::class) {
      ImageNode::class => $this->syncImage($node, $context),
      VideoNode::class => $this->syncVideo($node, $context),
      RemoteVideoNode::class => $this->syncRemoteVideo($node, $context),
      default => NULL,
    };
  }

  private function replaceNodeWithMedia(ContentNode $original_node, string $asset_source, array $media_metadata = []): void {
    $media = $this->mediaSynchronizer->sync($asset_source);
    if ($media) {
      // @todo Add 'media_metadata' support.
      $media_node = new DrupalMediaNode($media->uuid());
      $original_node->getParent()->replaceChild($original_node, $media_node);
    }
    else {
      $original_node->getParent()->removeChild($original_node);
    }
  }

  private function syncImage(ImageNode $node, ArticleTranslationProcessContext $context): void {
    $asset_path = $context->articleTranslation->contentDirectory . '/' . $node->getSrc();
    $data = [
      'src' => $node->getSrc(),
      'alt' => $node->getAlt(),
    ];
    $this->replaceNodeWithMedia($node, $asset_path, $data);
  }

  private function syncVideo(VideoNode $node, ArticleTranslationProcessContext $context): void {
    $asset_path = $context->articleTranslation->contentDirectory . '/' . $node->getSrc();
    $data = [
      'title' => $node->getTitle(),
    ];
    $this->replaceNodeWithMedia($node, $asset_path, $data);
  }

  private function syncRemoteVideo(RemoteVideoNode $node, ArticleTranslationProcessContext $context): void {
    $this->replaceNodeWithMedia($node, $node->getUrl());
  }

}
