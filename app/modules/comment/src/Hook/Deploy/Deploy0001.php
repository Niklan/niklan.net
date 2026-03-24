<?php

declare(strict_types=1);

namespace Drupal\app_comment\Hook\Deploy;

use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Unifies comment body format to 'comments' for all existing comments.
 */
final class Deploy0001 implements ContainerInjectionInterface {

  private const string TARGET_FORMAT = 'comments';

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('database'),
    );
  }

  public function __construct(
    private Connection $database,
  ) {}

  public function __invoke(array &$sandbox): string {
    if (!isset($sandbox['total'])) {
      $count = $this->database
        ->select('comment__comment_body', 'c')
        ->condition('c.comment_body_format', self::TARGET_FORMAT, '<>')
        ->countQuery()
        ->execute()
        ?->fetchField();
      \assert(\is_numeric($count));
      $sandbox['total'] = (int) $count;
      $sandbox['updated'] = 0;
      $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
    }

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;
      return 'No comments need format update.';
    }

    $entity_ids = $this->database
      ->select('comment__comment_body', 'c')
      ->fields('c', ['entity_id'])
      ->condition('c.comment_body_format', self::TARGET_FORMAT, '<>')
      ->range(0, $sandbox['limit'])
      ->execute()
      ?->fetchCol() ?? [];

    if ($entity_ids !== []) {
      $this->database
        ->update('comment__comment_body')
        ->fields(['comment_body_format' => self::TARGET_FORMAT])
        ->condition('entity_id', $entity_ids, 'IN')
        ->execute();
    }

    $sandbox['updated'] += \count($entity_ids);
    $sandbox['#finished'] = $sandbox['updated'] / $sandbox['total'];

    return \sprintf('%d of %d comments updated.', $sandbox['updated'], $sandbox['total']);
  }

}
