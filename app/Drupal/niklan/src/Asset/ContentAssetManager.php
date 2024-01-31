<?php declare(strict_types = 1);

namespace Drupal\niklan\Asset;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityTypeManagerInterface;
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
  ) {}

  /**
   * {@selfdoc}
   */
  public function saveMedia(string $path): ?MediaInterface {
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
  public function saveFile(string $path): ?FileInterface {
    return NULL;
  }

}
