<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Drupal\niklan\Entity\File\FileInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Calculate checksums for files.
 *
 * @see niklan_deploy_0003()
 */
final class Deploy0003 implements ContainerInjectionInterface {

  /**
   * Constructs a new Deploy0003 instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Prepares variables for batch if they are not initialized.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function prepareBatch(array &$sandbox): void {
    if (isset($sandbox['total'])) {
      return;
    }

    $sandbox['total'] = $this->getQuery()->count()->execute();
    $sandbox['current'] = 0;
    $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
  }

  /**
   * Process a single batch.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function processBatch(array &$sandbox): void {
    $ids = $this
      ->getQuery()
      ->range($sandbox['current'], $sandbox['limit'])
      ->execute();

    $files = $this
      ->entityTypeManager
      ->getStorage('file')
      ->loadMultiple($ids);

    foreach ($files as $file) {
      \assert($file instanceof FileInterface);
      $file->save();

      $sandbox['current']++;
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];
  }

  /**
   * Builds a default query for update.
   */
  protected function getQuery(): QueryInterface {
    return $this
      ->entityTypeManager
      ->getStorage('file')
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('fid');
  }

  /**
   * Implements hook_deploy_HOOK().
   */
  public function __invoke(array &$sandbox): string {
    $this->prepareBatch($sandbox);

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;

      return 'No files were found.';
    }

    $this->processBatch($sandbox);

    return (string) new FormattableMarkup('@current of @total files are processed.', [
      '@current' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

}
