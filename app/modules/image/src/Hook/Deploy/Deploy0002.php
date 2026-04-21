<?php

declare(strict_types=1);

namespace Drupal\app_image\Hook\Deploy;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Deploy0002 implements ContainerInjectionInterface {

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('file_system'),
    );
  }

  public function __construct(
    private FileSystemInterface $fileSystem,
  ) {}

  public function __invoke(): string {
    $deleted = [];
    foreach (['public', 'private'] as $scheme) {
      $dir = $scheme . '://styles/dynamic';
      if (!\is_dir($dir)) {
        continue;
      }

      $this->fileSystem->deleteRecursive($dir);
      $deleted[] = $scheme;
    }

    if ($deleted === []) {
      return 'No dynamic style directories found.';
    }

    return \sprintf('Deleted styles/dynamic in: %s.', \implode(', ', $deleted));
  }

}
