<?php declare(strict_types = 1);

namespace Drupal\niklan\Asset;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Helper\FileHelper;

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
    // External URLs are not supported, and most likely never will be here.
    // If you are interested in implementation, check this:
    // https://github.com/Druki-ru/website/blob/b40b30fccc2b3429424aea540d9804b27beed22e/web/modules/custom/druki/src/Repository/MediaImageRepository.php#L124-L126
    if (UrlHelper::isExternal($path)) {
      return NULL;
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
  private function syncWithFile(string $path): ?FileInterface {
    $checksum = FileHelper::checksum($path);

    // If checksum is not calculated, then file is exist or not accessible.
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
    // Key - the mime type regex.
    // Value - the media type to save into.
    // First match will be used as a media type.
    $map = [
      '/image\/svg.+/' => 'file',
      '/image\/.+/' => 'image',
      '/video\/.+/' => 'video',
      '/application\/.+/' => 'file',
      '/text\/.+/' => 'file',
    ];
    $media_type = NULL;

    foreach ($map as $regex => $media_type_association) {
      if (\preg_match($regex, $file->getMimeType()) === 1) {
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
  private function saveFileToMediaWithFileReference(FileInterface $file, string $type): MediaInterface {
    $media = $this
      ->entityTypeManager
      ->getStorage('media')
      ->create(['bundle' => $type]);
    \assert($media instanceof MediaInterface);
    $source_field = $media->getSource()->getConfiguration()['source_field'];
    $media->setName($file->label());
    $media->set($source_field, $file);
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
