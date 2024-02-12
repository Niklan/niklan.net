<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\Core\Template\Attribute;
use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Node\DrupalMediaElement;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final class DrupalMediaElementRenderArrayBuilder implements BuilderInterface, ContainerAwareInterface {

  /**
   * {@selfdoc}
   */
  private ContainerInterface $container;

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    \assert($node instanceof DrupalMediaElement);
    $media = $this->findMedia($node->uuid);

    if (!$media instanceof MediaInterface) {
      return BuilderResult::none();
    }

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
    return $type === RenderArrayBuilder::class && $node instanceof DrupalMediaElement;
  }

  /**
   * {@selfdoc}
   */
  public function setContainer(?ContainerInterface $container): void {
    $this->container = $container;
  }

  /**
   * {@selfdoc}
   */
  private function findMedia(string $uuid): ?MediaInterface {
    $storage = $this
      ->container
      ->get('entity_type.manager')
      ->getStorage('media');

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
  private function buildImageRenderArray(MediaInterface $media, DrupalMediaElement $node): BuilderResultInterface {
    $file = $this->getMediaSourceFile($media);

    if (!$file instanceof FileInterface) {
      return BuilderResult::none();
    }

    return BuilderResult::renderArray([
      '#theme' => 'niklan_lightbox_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $node->alt,
      '#title' => $node->title,
      '#thumbnail_responsive_image_style_id' => 'paragraph_image_image',
      '#lightbox_image_style_id' => 'big_image',
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function buildVideoRenderArray(MediaInterface $media): BuilderResultInterface {
    $file = $this->getMediaSourceFile($media);

    if (!$file instanceof FileInterface) {
      return BuilderResult::none();
    }

    $source_attributes = new Attribute();
    $source_attributes
      ->setAttribute('src', $file->createFileUrl())
      ->setAttribute('type', $file->getMimeType());
    $source_item = [
      'file' => $file,
      'source_attributes' => $source_attributes,
    ];

    return BuilderResult::renderArray([
      '#theme' => 'file_video',
      '#attributes' => [
        'width' => 640,
        'height' => 480,
        'controls' => 'controls',
        'loop' => 'loop',
        'muted' => TRUE,
      ],
      '#files' => [$source_item],
    ]);
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
    $view_builder = $this
      ->container
      ->get('entity_type.manager')
      ->getViewBuilder('media');

    return BuilderResult::renderArray($view_builder->view($media));
  }

}
