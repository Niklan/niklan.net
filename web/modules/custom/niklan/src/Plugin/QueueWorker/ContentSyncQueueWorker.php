<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\external_content\Data\ExternalContent;

/**
 * Provides a queue worker for content synchronization.
 *
 * @QueueWorker(
 *   id = "content_sync",
 *   title = @Translation("Content Synchronization"),
 * )
 *
 * @ingroup content_sync
 */
final class ContentSyncQueueWorker extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem(mixed $data): void {
    if (!$data instanceof ExternalContent) {
      return;
    }

    // @todo Create loader plugin and use it here.
  }

}
