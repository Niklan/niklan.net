<?php

declare(strict_types=1);

namespace Drupal\niklan\Media\Synchronizer;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\FileInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaStorage;
use Drupal\media\MediaTypeInterface;
use Drupal\niklan\File\Contract\FileSynchronizer;
use Drupal\niklan\Media\Contract\MediaSynchronizer;
use Drupal\niklan\Utils\YouTubeHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class DatabaseMediaSynchronizer implements MediaSynchronizer {

  public function __construct(
    private FileSynchronizer $fileSynchronizer,
    private EntityTypeManagerInterface $entityTypeManager,
    #[Autowire(service: 'file.usage')]
    private FileUsageInterface $fileUsage,
    #[Autowire(service: 'logger.channel.niklan')]
    private LoggerInterface $logger,
  ) {}

  public function sync(string $path): ?MediaInterface {
    // Replace spaces '%20' with an actual space. Without that, it can lead to
    // a wrong file detection.
    $normalized_path = \urldecode($path);

    // First handle special cases like YouTube.
    $media = $this->handleSpecialProviders($normalized_path);
    if ($media) {
      return $media;
    }

    return UrlHelper::isExternal($normalized_path)
      ? $this->handleExternalUrl($normalized_path)
      : $this->handleInternalPath($normalized_path);
  }

  private function handleSpecialProviders(string $url): ?MediaInterface {
    // Currently only supports YouTube.
    if (YouTubeHelper::isYouTubeUrl($url)) {
      return $this->syncYouTubeMedia($url);
    }
    return NULL;
  }

  private function handleExternalUrl(string $url): ?MediaInterface {
    $file = $this->fileSynchronizer->sync($url);
    if ($file) {
      return $this->resolveMediaForFile($file);
    }

    $this->logger->warning('Unsupported external URL', ['url' => $url]);
    return NULL;
  }

  private function handleInternalPath(string $path): ?MediaInterface {
    $file = $this->fileSynchronizer->sync($path);
    return $file ? $this->resolveMediaForFile($file) : NULL;
  }

  private function resolveMediaForFile(FileInterface $file): ?MediaInterface {
    $media = $this->findMediaByFileUsage($file);
    return $media ?? $this->createMediaForFile($file);
  }

  private function findMediaByFileUsage(FileInterface $file): ?MediaInterface {
    $usage = $this->fileUsage->listUsage($file);
    if (!isset($usage['file']['media'])) {
      return NULL;
    }

    // Since there is possible to have multiple usage of the same file in
    // different media entities through code and other modules, we just pick the
    // first one.
    $media_ids = \array_keys($usage['file']['media']);
    $media_id = \reset($media_ids);

    $media = $this->getMediaStorage()->load($media_id);
    return $media instanceof MediaInterface ? $media : NULL;
  }

  private function createMediaForFile(FileInterface $file): ?MediaInterface {
    $mime_type = $file->getMimeType();
    $media_type = $this->determineMediaType($mime_type);

    if (!$mime_type || !$media_type) {
      $this->logger->warning('Unsupported file type', [
        'mime' => $mime_type,
        'file' => $file->id(),
      ]);
      return NULL;
    }

    $source_field = $this->getMediaTypeSourceField($media_type);
    $media = $this->getMediaStorage()->create(['bundle' => $media_type]);
    \assert($media instanceof MediaInterface);
    $media->set($source_field, $file);
    $media->setName($file->getFilename());
    $media->save();

    return $media;
  }

  private function getMediaStorage(): MediaStorage {
    return $this->entityTypeManager->getStorage('media');
  }

  private function determineMediaType(string $mime): ?string {
    return match(TRUE) {
      \str_starts_with($mime, 'image/svg') => 'file',
      \str_starts_with($mime, 'image/') => 'image',
      \str_starts_with($mime, 'video/') => 'video',
      \str_starts_with($mime, 'application/') => 'file',
      \str_starts_with($mime, 'text/') => 'file',
      default => NULL,
    };
  }

  private function syncYouTubeMedia(string $url): ?MediaInterface {
    $video_id = YouTubeHelper::extractVideoId($url);
    if (!$video_id) {
      $this->logger->warning('Invalid YouTube URL', ['url' => $url]);
      return NULL;
    }

    $media_type = 'remote_video';
    $source_field = $this->getMediaTypeSourceField($media_type);
    $standard_url = "https://youtu.be/{$video_id}";

    $media = $this->findMediaBySourceField($media_type, $source_field, $standard_url);
    if ($media) {
      return $media;
    }

    $media = $this->getMediaStorage()->create(['bundle' => $media_type]);
    \assert($media instanceof MediaInterface);
    $media->set($source_field, $standard_url);
    $media->setName("YouTube: $video_id");
    $media->save();

    return $media;
  }

  private function findMediaBySourceField(string $bundle, string $source_field, string $value): ?MediaInterface {
    $ids = $this
      ->getMediaStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('bundle', $bundle)
      ->condition($source_field, $value)
      ->range(0, 1)
      ->execute();

    if (!$ids) {
      return NULL;
    }

    $media = $this->getMediaStorage()->load(\reset($ids));
    return $media instanceof MediaInterface ? $media : NULL;
  }

  private function getMediaTypeSourceField(string $media_type_id): string {
    $media_type = $this->entityTypeManager->getStorage('media_type')->load($media_type_id);
    \assert($media_type instanceof MediaTypeInterface);
    return $media_type->getSource()->getConfiguration()['source_field'];
  }

}
