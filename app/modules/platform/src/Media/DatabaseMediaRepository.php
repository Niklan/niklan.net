<?php

declare(strict_types=1);

namespace Drupal\app_platform\Media;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\FileInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaStorage;
use Drupal\app_contract\Contract\Media\MediaRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class DatabaseMediaRepository implements MediaRepository {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    #[Autowire(service: 'file.usage')]
    private FileUsageInterface $fileUsage,
  ) {}

  public function findByFile(FileInterface $file): ?MediaInterface {
    $usage = $this->fileUsage->listUsage($file);
    if (!isset($usage['file']['media'])) {
      return NULL;
    }

    // Since there is possible to have multiple usage of the same file in
    // different media entities through code and other modules, we just pick the
    // first one.
    $media_ids = \array_keys($usage['file']['media']);
    $media_id = \reset($media_ids);
    \assert(\is_numeric($media_id));

    $media = $this->getStorage()->load($media_id);
    return $media instanceof MediaInterface ? $media : NULL;
  }

  public function findBySourceField(string $bundle, string $source_field, string $value): ?MediaInterface {
    $ids = $this
      ->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('bundle', $bundle)
      ->condition($source_field, $value)
      ->range(0, 1)
      ->execute();

    if (!$ids) {
      return NULL;
    }

    $media = $this->getStorage()->load(\reset($ids));
    return $media instanceof MediaInterface ? $media : NULL;
  }

  private function getStorage(): MediaStorage {
    return $this->entityTypeManager->getStorage('media');
  }

}
