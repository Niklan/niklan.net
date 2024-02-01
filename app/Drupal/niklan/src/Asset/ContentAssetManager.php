<?php declare(strict_types = 1);

namespace Drupal\niklan\Asset;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityTypeManagerInterface;
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
  ) {}

  /**
   * {@selfdoc}
   */
  public function saveMedia(string $path): ?MediaInterface {
    $file = $this->saveFile($path);

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
      return $this->prepareMediaForFile($file);
    }

    $media = $this->entityTypeManager->getStorage('media')->load($media_id);

    if ($media instanceof MediaInterface) {
      return $media;
    }

    return $this->prepareMediaForFile($file);
  }

  /**
   * {@selfdoc}
   */
  public function saveFile(string $path): ?FileInterface {
    // External URLs are not supported, and most likely never will be here.
    // If you are interested in implementation, check this:
    // https://github.com/Druki-ru/website/blob/b40b30fccc2b3429424aea540d9804b27beed22e/web/modules/custom/druki/src/Repository/MediaImageRepository.php#L124-L126
    if (UrlHelper::isExternal($path)) {
      return NULL;
    }

    $checksum = FileHelper::fileChecksum($path);

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
      return NULL;
    }

    /* @phpstan-ignore-next-line */
    return $file_storage->load(\reset($file_ids));
  }

  /**
   * {@selfdoc}
   */
  private function prepareMediaForFile(FileInterface $file): ?MediaInterface {
    // Key - the mime type regex.
    // Value - the media type to save into.
    // First match will be used as a media type.
    $map = [
      '/image\/svg.+/' => 'file',
      '/image\/.+/' => 'image',
      '/video\/.+/' => 'video',
      '/application\/.+' => 'file',
      '/text\/.+' => 'file',
    ];
    $media_type = NULL;

    foreach ($map as $regex => $media_type_association) {
      if (\preg_match($regex, $file->getMimeType()) === 1) {
        $media_type = $media_type_association;

        break;
      }
    }

    if (!$media_type) {
      return NULL;
    }

    // @todo Implement something like that:
    //   https://github.com/Druki-ru/website/blob/b40b30fccc2b3429424aea540d9804b27beed22e/web/modules/custom/druki/src/Repository/MediaImageRepository.php#L233
    return NULL;
  }

}
