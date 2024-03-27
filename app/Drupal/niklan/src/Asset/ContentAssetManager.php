<?php declare(strict_types = 1);

namespace Drupal\niklan\Asset;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaTypeInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Helper\FileHelper;
use Drupal\niklan\Helper\YouTubeHelper;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class ContentAssetManager {

  /**
   * Constructs a new ContentAssetManager instance.
   */
  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private FileUsageInterface $fileUsage,
    private FileSystemInterface $fileSystem,
    private UuidInterface $uuid,
  ) {}

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
  private function syncWithFile(string $path): ?FileInterface {
    $checksum = FileHelper::checksum($path);

    // If checksum is not calculated, then file is not exist or not accessible.
    // Either way, this should be ended right here.
    if (!$checksum) {
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

    /* @phpstan-ignore-next-line */
    return $file_storage->load(\reset($file_ids));
  }

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
  private function saveToFile(string $path): ?FileInterface {
    $destination = $this->prepareDestination($path);

    if (!$this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      return NULL;
    }

    $destination_uri = $destination . \DIRECTORY_SEPARATOR . $this->prepareFilename($path);
    $this->fileSystem->copy($path, $destination_uri);

    $file_storage = $this->entityTypeManager->getStorage('file');
    $file = $file_storage->create();
    \assert($file instanceof FileInterface);
    $file->setFileUri($destination_uri);
    $file->setPermanent();
    $file->save();

    return $file;
  }

  /**
   * {@selfdoc}
   */
  private function saveFileToMediaWithFileReference(FileInterface $file, string $type): MediaInterface {
    $media = $this
      ->entityTypeManager
      ->getStorage('media')
      ->create(['bundle' => $type]);
    \assert($media instanceof MediaInterface);
    $media->setName($file->label());
    $media->set($this->getMediaTypeSourceField($type), $file);
    $media->save();

    return $media;
  }

  /**
   * {@selfdoc}
   */
  private function getMediaTypeSourceField(string $media_type_id): string {
    $media_type = $this
      ->entityTypeManager
      ->getStorage('media_type')
      ->load($media_type_id);
    \assert($media_type instanceof MediaTypeInterface);

    return $media_type->getSource()->getConfiguration()['source_field'];
  }

  /**
   * {@selfdoc}
   */
  private function prepareDestination(string $path): string {
    $datetime = new \DateTime();

    return \implode(\DIRECTORY_SEPARATOR, [
      'public:/',
      $datetime->format('Y') . '-' . $datetime->format('m'),
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function prepareFilename(string $path): string {
    $extension = FileHelper::extension($path);

    return "{$this->uuid->generate()}.{$extension}";
  }

}
