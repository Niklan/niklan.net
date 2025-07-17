<?php

declare(strict_types=1);

namespace Drupal\niklan\Blog\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\niklan\Blog\Contract\BlogRepository;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\node\NodeStorageInterface;

final readonly class DatabaseBlogRepository implements BlogRepository {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function findByExternalId(string $external_id): ?BlogEntry {
    $ids = $this
      ->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();
    // @phpstan-ignore-next-line
    return $ids ? $this->getStorage()->load(\reset($ids)) : NULL;
  }

  private function getStorage(): NodeStorageInterface {
    return $this->entityTypeManager->getStorage('node');
  }

}
