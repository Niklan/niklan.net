<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\media\MediaInterface;
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

    return BuilderResult::renderArray([
      // @todo Prepare render array with media.
      '#markup' => $media->uuid(),
    ]);
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

}
