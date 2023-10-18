<?php declare(strict_types = 1);

namespace Drupal\niklan\Queue;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\external_content\Contract\Finder\FinderFacadeInterface;
use Drupal\external_content\Data\ExternalContent;
use Drupal\external_content\Data\SourceConfiguration;

/**
 * Provides a content sync queue.
 *
 * @ingroup content_sync
 */
final class ContentSyncQueueManager {

  /**
   * The queue name.
   */
  protected const QUEUE_NAME = 'blog_content_sync';

  /**
   * Constructs a new ContentSyncQueueManager instance.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queueFactory
   *   The queue factory.
   * @param \Drupal\external_content\Contract\Finder\FinderFacadeInterface $externalContentFinder
   *   The external content finder.
   */
  public function __construct(
    protected QueueFactory $queueFactory,
    protected FinderFacadeInterface $externalContentFinder,
  ) {}

  /**
   * Builds the queue.
   *
   * @param \Drupal\external_content\Data\SourceConfiguration $configuration
   *   The source content configuration.
   */
  public function buildQueue(SourceConfiguration $configuration): self {
    $this->clearQueue();

    $external_content_collection = $this
      ->externalContentFinder
      ->find($configuration);

    foreach ($external_content_collection as $external_content) {
      \assert($external_content instanceof ExternalContent);
      $this->getQueue()->createItem($external_content);
    }

    return $this;
  }

  /**
   * Clears the queue items.
   */
  public function clearQueue(): self {
    $this->getQueue()->deleteQueue();

    return $this;
  }

  /**
   * Gets the queue instance.
   */
  public function getQueue(): QueueInterface {
    return $this->queueFactory->get(self::QUEUE_NAME);
  }

}
