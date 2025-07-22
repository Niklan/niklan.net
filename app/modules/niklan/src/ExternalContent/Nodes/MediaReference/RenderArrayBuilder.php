<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\MediaReference;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Template\Attribute;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;
use Drupal\media\MediaInterface;
use Drupal\niklan\Utils\MediaHelper;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\niklan\ExternalContent\Nodes\MediaReference\MediaReference>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function supports(Node $node): bool {
    return $node instanceof MediaReference;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $media = $this->loadByUuid($node);
    if (!$media) {
      return new RenderArray([
        '#markup' => 'Missing media with UUID: ' . $node->uuid . '.',
      ]);
    }

    $build = match ($media->bundle()) {
      'image' => $this->buildImage($node, $media),
      'video' => $this->buildVideo($node, $media),
      'remote_video' => $this->buildRemoteVideo($node, $media),
      default => throw new \RuntimeException('Unsupported media bundle: ' . $media->bundle()),
    };
    $build->cacheableMetadata->addCacheableDependency($media);
    return $build;
  }

  private function buildImage(MediaReference $node, MediaInterface $media): RenderArray {
    $file = MediaHelper::getFile($media);
    if (!$file) {
      return new RenderArray([
        '#markup' => 'Missing file for media with UUID: ' . $node->uuid . '.',
      ]);
    }

    $build = new RenderArray([
      '#theme' => 'niklan_lightbox_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $node->metadata['alt'] ?? NULL,
      '#thumbnail_responsive_image_style_id' => 'paragraph_image_image',
      '#lightbox_image_style_id' => 'big_image',
    ]);
    $build->cacheableMetadata->addCacheableDependency($file);
    return $build;
  }

  private function buildVideo(MediaReference $node, MediaInterface $media): RenderArray {
    $file = MediaHelper::getFile($media);
    if (!$file || !$file->getMimeType()) {
      return new RenderArray([
        '#markup' => 'Missing file or mimetype for media with UUID: ' . $node->uuid . '.',
      ]);
    }

    $source_attributes = new Attribute();
    $source_attributes->setAttribute('src', $file->createFileUrl());
    $source_attributes->setAttribute('type', $file->getMimeType());

    $video_attributes = [
      'width' => 640,
      'height' => 480,
      ...$node->metadata,
    ];

    $render_array = new RenderArray([
      '#theme' => 'file_video',
      '#attributes' => $video_attributes,
      '#files' => [
        [
          'file' => $file,
          'source_attributes' => $source_attributes,
        ],
      ],
    ]);
    $render_array->cacheableMetadata->addCacheableDependency($file);
    return $render_array;
  }

  private function buildRemoteVideo(MediaReference $node, MediaInterface $media): RenderArray {
    $view_builder = $this->entityTypeManager->getViewBuilder('media');
    return new RenderArray($view_builder->view($media));
  }

  private function loadByUuid(MediaReference $node): ?MediaInterface {
    $storage = $this->entityTypeManager->getStorage('media');
    $ids = $storage->getQuery()->accessCheck(FALSE)->condition('uuid', $node->uuid)->range(0, 1)->sort('mid')->execute();
    if (!$ids) {
      return NULL;
    }
    return $storage->load(\reset($ids));
  }

}
