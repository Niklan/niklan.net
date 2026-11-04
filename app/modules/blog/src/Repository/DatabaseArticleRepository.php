<?php

declare(strict_types=1);

namespace Drupal\app_blog\Repository;

use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_contract\Contract\Blog\ArticleRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeStorageInterface;

final readonly class DatabaseArticleRepository implements ArticleRepository {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function findByExternalId(string $external_id): ?ArticleBundle {
    $ids = $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();
    // @phpstan-ignore-next-line
    return $ids ? $this->getStorage()->load(\reset($ids)) : NULL;
  }

  #[\Override]
  public function findAllExceptExternalIds(array $external_ids): array {
    $query = $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry');
    if ($external_ids !== []) {
      $query->condition('external_id', $external_ids, 'NOT IN');
    }
    $ids = $query->execute();

    // @phpstan-ignore-next-line
    return \array_values($this->getStorage()->loadMultiple($ids));
  }

  private function getStorage(): NodeStorageInterface {
    return $this->entityTypeManager->getStorage('node');
  }

}
