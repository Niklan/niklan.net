<?php

declare(strict_types=1);

namespace Drupal\niklan\File\Synchronizer;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\niklan\File\Contract\FileSynchronizer;
use Drupal\niklan\File\Entity\FileInterface;
use Drupal\niklan\File\Utils\FileHelper;
use Drupal\niklan\Utils\PathHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

final readonly class DatabaseFileSynchronizer implements FileSynchronizer {

  private const string CHECKSUM_FIELD = 'niklan_checksum';

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private FileSystemInterface $fileSystem,
    private UuidInterface $uuid,
    private TimeInterface $time,
    #[Autowire(service: 'file.mime_type.guesser')]
    private MimeTypeGuesserInterface $mimeTypeGuesser,
    #[Autowire(service: 'logger.channel.niklan')]
    private LoggerInterface $logger,
  ) {}

  public function sync(string $path): ?FileInterface {
    $normalized_path = PathHelper::normalizePath($path);
    return UrlHelper::isExternal($normalized_path)
      ? $this->handleExternalUrl($normalized_path)
      : $this->syncInternal($normalized_path);
  }

  private function handleExternalUrl(string $url): ?FileInterface {
    // External URLs are not supported at the moment, as there is no need for
    // that. If you are interested in how to do this, check the following:
    // https://github.com/Druki-ru/website/blob/b40b30fccc2b3429424aea540d9804b27beed22e/web/modules/custom/druki/src/Repository/MediaImageRepository.php#L124-L126
    $this->logger->debug('External URLs are not supported', ['url' => $url]);
    return NULL;
  }

  private function syncInternal(string $path): ?FileInterface {
    $checksum = FileHelper::checksum($path);

    if (!$checksum) {
      $this->logger->warning('Failed to calculate file checksum', ['path' => $path]);
      return NULL;
    }

    return $this->findFileByChecksum($checksum, $path) ?? $this->saveToFile($path);
  }

  private function findFileByChecksum(string $checksum, string $source_path): ?FileInterface {
    $file_id = $this->findExistingFileId($checksum);
    if (!$file_id) {
      return NULL;
    }

    $file = $this->getFileStorage()->load($file_id);
    if (!$file instanceof FileInterface) {
      $this->logger->error('Missing file entity', ['fid' => $file_id]);
      return NULL;
    }

    $this->ensurePhysicalFileExists($file, $source_path);
    return $file;
  }

  private function findExistingFileId(string $checksum): ?int {
    $result = $this
      ->getFileStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition(self::CHECKSUM_FIELD, $checksum)
      ->range(0, 1)
      ->sort('fid', 'DESC')
      ->execute();

    return $result ? (int) \reset($result) : NULL;
  }

  private function getFileStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('file');
  }

  private function ensurePhysicalFileExists(FileInterface $file, string $source_path): void {
    $file_uri = $file->getFileUri();
    if (!$file_uri) {
      $this->logger->error('File entity has empty URI', ['fid' => $file->id()]);
      return;
    }

    if (\file_exists($file_uri)) {
      return;
    }

    $destination_directory = \dirname($file_uri);
    $is_directory_prepared = $this->fileSystem->prepareDirectory(
      directory: $destination_directory,
      options: FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS,
    );
    if (!$is_directory_prepared) {
      $this->logger->error('Directory preparation failed', ['directory' => $destination_directory]);
      return;
    }

    $this->logger->info('Restoring missing file from source', [
      'source' => $source_path,
      'destination' => $file_uri,
    ]);
    $result = $this->fileSystem->copy($source_path, $file_uri, FileExists::Replace);
    if ($result) {
      return;
    }

    $this->logger->error('File copy failed', [
      'source' => $source_path,
      'destination' => $file_uri,
    ]);
  }

  private function saveToFile(string $source_path): ?FileInterface {
    $destination_directory = $this->prepareDestinationDirectory();
    $is_directory_prepared = $this->fileSystem->prepareDirectory(
      directory: $destination_directory,
      options: FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS,
    );
    if (!$is_directory_prepared) {
      $this->logger->error('Directory preparation failed', ['directory' => $destination_directory]);
      return NULL;
    }

    $destination_filename = $this->prepareDestinationFilename($source_path);
    $destination_uri = $this->fileSystem->getDestinationFilename(
      destination: $destination_directory . \DIRECTORY_SEPARATOR . $destination_filename,
      fileExists: FileExists::Error,
    );
    if (!$destination_uri) {
      $this->logger->error('Destination file URI is already exists', ['destination' => $destination_uri]);
      return NULL;
    }

    $this->fileSystem->copy($source_path, $destination_uri);
    $file = $this->getFileStorage()->create();
    \assert($file instanceof FileInterface);
    $file->setFilename($destination_filename);
    $file->setFileUri($destination_uri);
    $file->setMimeType($this->mimeTypeGuesser->guessMimeType($destination_uri));
    $file->setPermanent();
    $file->save();

    return $file;
  }

  private function prepareDestinationDirectory(): string {
    $current_time = $this->time->getCurrentTime();
    $year = \date('Y', $current_time);
    $month = \date('m', $current_time);
    return "public://$year-$month";
  }

  private function prepareDestinationFilename(string $path): string {
    $extension = FileHelper::extension($path);
    return "{$this->uuid->generate()}.{$extension}";
  }

}
