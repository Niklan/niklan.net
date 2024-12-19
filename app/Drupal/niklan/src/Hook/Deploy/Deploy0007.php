<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Deploy0007 implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  private function getQuery(): QueryInterface {
    return $this
      ->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', 0)
      ->sort('cid', 'DESC');
  }

  private function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('comment');
  }

  private function comment(array &$sandbox): \Generator {
    $ids = $this
      ->getQuery()
      ->range(0, $sandbox['limit'])
      ->execute();
    $sandbox['current'] += \count($ids);

    yield from $this->getStorage()->loadMultiple($ids);
  }

  public function __invoke(array &$sandbox): string {
    if (!isset($sandbox['total'])) {
      $sandbox['total'] = $this->getQuery()->count()->execute();
      $sandbox['current'] = 0;
      $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
    }

    if (!$sandbox['total']) {
      $sandbox['#finished'] = 1;

      return 'No comments found to delete.';
    }

    foreach ($this->comment($sandbox) as $comment) {
      $this->getStorage()->delete([$comment]);
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];

    return (string) new FormattableMarkup('@count/@total comments were deleted.', [
      '@count' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

}
