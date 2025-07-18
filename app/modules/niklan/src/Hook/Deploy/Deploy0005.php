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
use Drupal\niklan\File\Entity\FileInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Remove media files with references to an image.
 *
 * @see niklan_deploy_0005()
 */
final readonly class Deploy0005 implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  private function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('media');
  }

  private function getQuery(): QueryInterface {
    return $this
      ->getStorage()
      ->getQuery()
      ->condition('bundle', 'file')
      ->accessCheck(FALSE)
      ->sort('mid');
  }

  private function media(array &$sandbox): \Generator {
    $ids = $this
      ->getQuery()
      ->range(0, $sandbox['limit'])
      ->execute();
    \assert(\is_array($ids));
    $sandbox['current'] += \count($ids);

    yield from $this->getStorage()->loadMultiple($ids);
  }

  private function process(MediaInterface $media): void {
    $file = $media
      ->get($media->getSource()->getConfiguration()['source_field'])
      ->first()
      ?->get('entity')
        ->getValue();

    if (!$file instanceof FileInterface) {
      return;
    }

    // @phpstan-ignore-next-line
    if (!\str_starts_with($file->getMimeType(), 'image/')) {
      return;
    }

    // Just delete the media, next time content is syncing, it will create
    // a proper media for the file.
    $media->delete();
  }

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
      \assert($media instanceof MediaInterface);
      $this->process($media);
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];

    return (string) new FormattableMarkup('@count/@total media were processed.', [
      '@count' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

}
