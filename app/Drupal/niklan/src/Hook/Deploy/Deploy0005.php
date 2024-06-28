<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Remove media files with references to an image.
 *
 * @see niklan_deploy_0005()
 */
final readonly class Deploy0005 implements ContainerInjectionInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  /**
   * {@selfdoc}
   */
  public function __invoke(array &$sandbox): string {
    if (!isset($sandbox['total'])) {
      $sandbox['total'] = $this->getQuery()->count()->execute();
      $sandbox['current'] = 0;
      $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
    }

    if (!$sandbox['total']) {
      $sandbox['#finished'] = 1;

      return 'No media entities to process.';
    }

    foreach ($this->media($sandbox) as $media) {
      $this->process($media);
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];

    return (string) new FormattableMarkup('@count/@total media were processed.', [
      '@count' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('media');
  }

  /**
   * {@selfdoc}
   */
  private function getQuery(): QueryInterface {
    return $this
      ->getStorage()
      ->getQuery()
      ->condition('bundle', 'file')
      ->accessCheck(FALSE)
      ->sort('mid');
  }

  /**
   * {@selfdoc}
   */
  private function media(array &$sandbox): \Generator {
    $ids = $this
      ->getQuery()
      ->range(0, $sandbox['limit'])
      ->execute();
    $sandbox['current'] += \count($ids);

    yield from $this->getStorage()->loadMultiple($ids);
  }

  /**
   * {@selfdoc}
   */
  private function process(MediaInterface $media): void {
    $file = $media
      ->get($media->getSource()->getConfiguration()['source_field'])
      ->first()
      ?->get('entity')
        ->getValue();

    if (!$file instanceof FileInterface) {
      return;
    }

    if (!\str_starts_with($file->getMimeType(), 'image/')) {
      return;
    }

    // Just delete the media, next time content is syncing, it will create
    // a proper media for the file.
    $media->delete();
  }

}
