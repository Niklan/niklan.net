<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder\ExternalContent\RenderArray;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Template\Attribute;
use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Node\ExternalContent\DrupalMedia as DrupalMediaNode;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class DrupalMedia implements BuilderInterface {

  /**
   * {@selfdoc}
   */
  private CacheableMetadata $cache;

  /**
   * {@selfdoc}
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    \assert($node instanceof DrupalMediaNode);
    $media = $this->findMedia($node->uuid);

    if (!$media instanceof MediaInterface) {
      return BuilderResult::none();
    }

    $this->cache = new CacheableMetadata();
    $this->cache->addCacheableDependency($media);

    return match ($media->bundle()) {
      'image' => $this->buildImageRenderArray($media, $node),
      'video' => $this->buildVideoRenderArray($media),
      'remote_video' => $this->buildRemoteVideoRenderArray($media),
      default => BuilderResult::none(),
    };
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === RenderArrayBuilder::class && $node instanceof DrupalMediaNode;
  }

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
  private function buildImageRenderArray(MediaInterface $media, DrupalMediaNode $node): BuilderResultInterface {
    $file = $this->getMediaSourceFile($media);

    if (!$file instanceof FileInterface) {
      return BuilderResult::none();
    }

    $this->cache->addCacheableDependency($file);
    $build = [
      '#theme' => 'niklan_lightbox_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $node->alt,
      '#title' => $node->title,
      '#thumbnail_responsive_image_style_id' => 'paragraph_image_image',
      '#lightbox_image_style_id' => 'big_image',
    ];
    $this->cache->applyTo($build);

    return BuilderResult::renderArray($build);
  }

  /**
   * {@selfdoc}
   */
  private function buildVideoRenderArray(MediaInterface $media): BuilderResultInterface {
    $file = $this->getMediaSourceFile($media);

    if (!$file instanceof FileInterface) {
      return BuilderResult::none();
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

    return BuilderResult::renderArray($build);
  }

  /**
   * {@selfdoc}
   */
  private function getMediaSourceFile(MediaInterface $media): ?FileInterface {
    $source_field = $media->getSource()->getConfiguration()['source_field'];

    return $media->get($source_field)->first()->get('entity')->getValue();
  }

  /**
   * {@selfdoc}
   */
  private function buildRemoteVideoRenderArray(MediaInterface $media): BuilderResultInterface {
    $view_builder = $this->entityTypeManager->getViewBuilder('media');

    return BuilderResult::renderArray($view_builder->view($media));
  }

}
