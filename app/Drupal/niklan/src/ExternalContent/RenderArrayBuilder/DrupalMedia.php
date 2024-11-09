<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\RenderArrayBuilder;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Template\Attribute;
use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\media\MediaInterface;
use Drupal\niklan\Content\File\Entity\FileInterface;
use Drupal\niklan\ExternalContent\Node\DrupalMedia as DrupalMediaNode;

/**
 * @ingroup content_sync
 */
final class DrupalMedia implements RenderArrayBuilderInterface {

  private CacheableMetadata $cache;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof DrupalMediaNode);
    $media = $this->findMedia($node->uuid);

    if (!$media instanceof MediaInterface) {
      return RenderArrayBuilderResult::empty();
    }

    $this->cache = new CacheableMetadata();
    $this->cache->addCacheableDependency($media);

    return match ($media->bundle()) {
      'image' => $this->buildImageRenderArray($media, $node),
      'video' => $this->buildVideoRenderArray($media),
      'remote_video' => $this->buildRemoteVideoRenderArray($media),
      default => RenderArrayBuilderResult::empty(),
    };
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof DrupalMediaNode;
  }

  private function findMedia(string $uuid): ?MediaInterface {
    $storage = $this->entityTypeManager->getStorage('media');

    $ids = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('uuid', $uuid)
      ->execute();

    if (!$ids) {
      return NULL;
    }

    return $storage->load(\reset($ids));
  }

  private function buildImageRenderArray(MediaInterface $media, DrupalMediaNode $node): RenderArrayBuilderResult {
    $file = $this->getMediaSourceFile($media);

    if (!$file instanceof FileInterface) {
      return RenderArrayBuilderResult::empty();
    }

    $this->cache->addCacheableDependency($file);
    $build = [
      '#theme' => 'niklan_lightbox_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $node->data->get('alt') ?? NULL,
      '#title' => $node->data->get('title') ?? NULL,
      '#thumbnail_responsive_image_style_id' => 'paragraph_image_image',
      '#lightbox_image_style_id' => 'big_image',
    ];
    $this->cache->applyTo($build);

    return RenderArrayBuilderResult::withRenderArray($build);
  }

  private function buildVideoRenderArray(MediaInterface $media): RenderArrayBuilderResult {
    $file = $this->getMediaSourceFile($media);

    if (!$file instanceof FileInterface) {
      return RenderArrayBuilderResult::empty();
    }

    $this->cache->addCacheableDependency($file);

    $source_attributes = new Attribute();
    $source_attributes
      ->setAttribute('src', $file->createFileUrl())
      ->setAttribute('type', $file->getMimeType());
    $source_item = [
      'file' => $file,
      'source_attributes' => $source_attributes,
    ];

    $build = [
      '#theme' => 'file_video',
      '#attributes' => [
        'width' => 640,
        'height' => 480,
        'controls' => 'controls',
        'loop' => 'loop',
        'muted' => TRUE,
      ],
      '#files' => [$source_item],
    ];
    $this->cache->applyTo($build);

    return RenderArrayBuilderResult::withRenderArray($build);
  }

  private function buildRemoteVideoRenderArray(MediaInterface $media): RenderArrayBuilderResult {
    $view_builder = $this->entityTypeManager->getViewBuilder('media');

    return RenderArrayBuilderResult::withRenderArray($view_builder->view($media));
  }

  private function getMediaSourceFile(MediaInterface $media): ?FileInterface {
    $source_field = $media->getSource()->getConfiguration()['source_field'];

    return $media->get($source_field)->first()->get('entity')->getValue();
  }

}
