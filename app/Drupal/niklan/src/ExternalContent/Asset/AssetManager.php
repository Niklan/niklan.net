<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Asset;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaTypeInterface;
use Drupal\niklan\File\Entity\FileInterface;
use Drupal\niklan\File\Utils\FileHelper;
use Drupal\niklan\Utils\YouTubeHelper;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * @ingroup content_sync
 */
final class AssetManager {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    #[Autowire(service: 'file.usage')]
    private FileUsageInterface $fileUsage,
    private FileSystemInterface $fileSystem,
    private UuidInterface $uuid,
    private TimeInterface $time,
    #[Autowire(service: 'file.mime_type.guesser')]
    private MimeTypeGuesserInterface $mimeTypeGuesser,
    #[Autowire(service: 'logger.channel.niklan')]
    private LoggerChannelInterface $logger,
  ) {}

  public function syncWithMedia(string $path): ?MediaInterface {
    // Replace spaces '%20' with an actual space. Without that, it can lead to
    // a wrong file detection.
    $path = \urldecode($path);

    if (UrlHelper::isExternal($path)) {
      return $this->syncExternal($path);
    }

    $file = $this->syncWithFile($path);

    if (!$file instanceof FileInterface) {
      return NULL;
    }

    $usage = $this->fileUsage->listUsage($file);
    // Since there is possible to have multiple usage of the same file in
    // different media entities through code and other modules, we just pick the
    // first one.
    $media_id = isset($usage['file']['media'])
      ? \array_keys($usage['file']['media'])[0]
      : NULL;

    if (!$media_id) {
      return $this->createMediaForFile($file);
    }

    $media = $this->entityTypeManager->getStorage('media')->load($media_id);

    if ($media instanceof MediaInterface) {
      return $media;
    }

    return $this->createMediaForFile($file);
  }

  private function syncExternal(string $url): ?MediaInterface {
    // External URLs are partially supported. Only YouTube URLs are
    // supported at this point. If you are interested in implementation that
    // supports other contents (e.g. images), check this:
    // https://github.com/Druki-ru/website/blob/b40b30fccc2b3429424aea540d9804b27beed22e/web/modules/custom/druki/src/Repository/MediaImageRepository.php#L124-L126
    if (!YouTubeHelper::isYouTubeUrl($url)) {
      return NULL;
    }

    return $this->syncYouTubeMedia($url);
  }

  private function syncWithFile(string $path): ?FileInterface {
    $checksum = FileHelper::checksum($path);

    // If checksum is not calculated, then file is not exist or not accessible.
    // Either way, this should be ended right here.
    if (!$checksum) {
      $this->logger->warning(\sprintf(
        'File checksum calculation failed for: %s',
        $path,
      ));

      return NULL;
    }

    $file_storage = $this->entityTypeManager->getStorage('file');
    $file_ids = $file_storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('niklan_checksum', $checksum)
      ->range(0, 1)
      ->sort('fid')
      ->execute();

    if (\count($file_ids) === 0) {
      return $this->saveToFile($path);
    }

    $file = $file_storage->load(\reset($file_ids));

    if (!$file instanceof FileInterface) {
      return NULL;
    }

    $this->ensureFileIsPresented($file, $path);

    return $file;
  }

  /**
   * Ensures the file is presented.
   *
   * It is mostly for the local environment, when the file entities are present
   * in the database, but the physically files are missing. This method copies
   * the file in that case to make sure they are in sync.
   */
  private function ensureFileIsPresented(FileInterface $file, string $path): void {
    $file_uri = $file->getFileUri();

    if (!\is_string($file_uri) || \file_exists($file_uri)) {
      return;
    }

    $destination = \pathinfo($file_uri, \PATHINFO_DIRNAME);

    if (!$this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      return;
    }

    $this->fileSystem->copy($path, $file_uri, FileExists::Replace);
  }

  private function createMediaForFile(FileInterface $file): ?MediaInterface {
    // Key - fnmatch pattern.
    // Value - the media type to save into.
    // First match will be used as a media type.
    $map = [
      'image/svg*' => 'file',
      'image/*' => 'image',
      'video/*' => 'video',
      'application/*' => 'file',
      'text/*' => 'file',
    ];
    $media_type = NULL;

    foreach ($map as $pattern => $media_type_association) {
      // phpcs:disable Drupal.Functions.DiscouragedFunctions.Discouraged
      // @phpstan-ignore-next-line
      if (\fnmatch($pattern, $file->getMimeType())) {
        // phpcs:enable
        $media_type = $media_type_association;

        break;
      }
    }

    return match ($media_type) {
      default => NULL,
      'image', 'file', 'video' => $this->saveFileToMediaWithFileReference(
        file: $file,
        type: $media_type,
      ),
    };
  }

  private function syncYouTubeMedia(string $url): ?MediaInterface {
    $video_id = YouTubeHelper::extractVideoId($url);

    if (!$video_id) {
      return NULL;
    }

    // Force URL to be in the same format.
    $youtube_url = "https://youtu.be/{$video_id}";

    $media_type_id = 'remote_video';
    $source_field = $this->getMediaTypeSourceField($media_type_id);
    $storage = $this->entityTypeManager->getStorage('media');
    $ids = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('bundle', $media_type_id)
      ->condition($source_field, $youtube_url)
      ->range(0, 1)
      ->execute();

    if ($ids) {
      return $storage->load(\reset($ids));
    }

    $media = $storage->create(['bundle' => $media_type_id]);
    \assert($media instanceof MediaInterface);
    $media->set($source_field, $youtube_url);
    $media->save();

    return $media;
  }

  private function saveToFile(string $path): ?FileInterface {
    $destination = $this->prepareDestination();

    if (!$this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      return NULL;
    }

    $filename = $this->prepareFilename($path);
    $destination_uri = $destination . \DIRECTORY_SEPARATOR . $this->prepareFilename($path);
    $this->fileSystem->copy($path, $destination_uri);

    $file_storage = $this->entityTypeManager->getStorage('file');
    $file = $file_storage->create();
    \assert($file instanceof FileInterface);
    $file->setFilename($filename);
    $file->setFileUri($destination_uri);
    $file->setMimeType($this->mimeTypeGuesser->guessMimeType($destination_uri));
    $file->setPermanent();
    $file->save();

    return $file;
  }

  private function saveFileToMediaWithFileReference(FileInterface $file, string $type): MediaInterface {
    $media = $this
      ->entityTypeManager
      ->getStorage('media')
      ->create(['bundle' => $type]);
    \assert($media instanceof MediaInterface);
    $media->setName((string) $file->label());
    $media->set($this->getMediaTypeSourceField($type), $file);
    $media->save();

    return $media;
  }

  private function getMediaTypeSourceField(string $media_type_id): string {
    $media_type = $this
      ->entityTypeManager
      ->getStorage('media_type')
      ->load($media_type_id);
    \assert($media_type instanceof MediaTypeInterface);

    return $media_type->getSource()->getConfiguration()['source_field'];
  }

  private function prepareDestination(): string {
    $datetime = new \DateTime();
    $datetime->setTimestamp($this->time->getCurrentTime());

    return \implode(\DIRECTORY_SEPARATOR, [
      'public:/',
      $datetime->format('Y') . '-' . $datetime->format('m'),
    ]);
  }

  private function prepareFilename(string $path): string {
    $extension = FileHelper::extension($path);

    return "{$this->uuid->generate()}.{$extension}";
  }

}
