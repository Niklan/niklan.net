<?php declare(strict_types = 1);

namespace Drupal\niklan\Manager;

use Drupal\external_content\Contract\SourcePluginInterface;
use Drupal\external_content\Contract\SourcePluginManagerInterface;
use Drupal\niklan\Queue\ContentSyncQueueManager;

/**
 * Provides content synchronization manager.
 *
 * @ingroup content_sync
 */
final class ContentSyncManager {

  /**
   * Constructs a new ContentSyncManager instance.
   *
   * @param \Drupal\external_content\Contract\SourcePluginManagerInterface $sourcePluginManager
   *   The source plugin manager.
   * @param \Drupal\niklan\Queue\ContentSyncQueueManager $contentSyncQueueManager
   *   The content sync queue manager.
   */
  public function __construct(
    protected SourcePluginManagerInterface $sourcePluginManager,
    protected ContentSyncQueueManager $contentSyncQueueManager,
  ) {}

  /**
   * Requests content synchronization.
   *
   * @return bool
   *   TRUE if synchronization requested, FALSE if something is wrong.
   */
  public function synchronize(): bool {
    $source_plugin = $this->sourcePluginManager->createInstance('content');

    if (!($source_plugin instanceof SourcePluginInterface)) {
      return FALSE;
    }

    if (!$source_plugin->isActive()) {
      return FALSE;
    }

    $source_configuration = $source_plugin->toConfiguration();
    $this->contentSyncQueueManager->buildQueue($source_configuration);

    return TRUE;
  }

}
