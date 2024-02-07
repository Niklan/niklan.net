<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

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
      'image' => $this->buildImageRenderArray($media, $node->alt, $context),
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
  private function buildImageRenderArray(MediaInterface $media, ?string $alt, array $context): BuilderResultInterface {
    $source_field = $media->getSource()->getConfiguration()['source_field'];
    $file = $media->get($source_field)->first()->get('entity')->getValue();

    if (!$file instanceof FileInterface) {
      return BuilderResult::none();
    }

    return BuilderResult::renderArray([
      '#theme' => 'niklan_lightbox_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $alt,
      '#thumbnail_responsive_image_style_id' => 'paragraph_image_image',
      '#lightbox_image_style_id' => 'big_image',
    ]);
  }

}
