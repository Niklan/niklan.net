<?php

declare(strict_types=1);

namespace Drupal\niklan\Tag\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\app_contract\Contract\Tag\TagRepository;
use Drupal\niklan\Tag\Entity\TagBundle;
use Drupal\taxonomy\TermStorageInterface;

final readonly class DatabaseTagRepository implements TagRepository {

  private const string VID = 'tags';

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function findByExternalId(string $external_id): ?TagBundle {
    $ids = $this
      ->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('vid', self::VID)
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();
    // @phpstan-ignore-next-line
    return \count($ids) ? $this->getStorage()->load(\reset($ids)) : NULL;
  }

  private function getStorage(): TermStorageInterface {
    return $this->entityTypeManager->getStorage('taxonomy_term');
  }

}
