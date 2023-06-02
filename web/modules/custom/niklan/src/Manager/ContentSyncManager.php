<?php declare(strict_types = 1);

namespace Drupal\niklan\Manager;

use Drupal\external_content\Contract\SourcePluginInterface;
use Drupal\external_content\Contract\SourcePluginManagerInterface;
use Drupal\niklan\Event\ContentSyncEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   */
  public function __construct(
    protected SourcePluginManagerInterface $sourcePluginManager,
    protected EventDispatcherInterface $eventDispatcher,
  ) {}

  /**
   * Requests content synchronization.
   *
   * @return bool
   *   TRUE if synchronization requested, FALSE if something is wrong.
   */
  public function synchronize(): bool {
    $source_plugin = $this->sourcePluginManager->createInstance('content');

    if (!$source_plugin instanceof SourcePluginInterface) {
      return FALSE;
    }

    if (!$source_plugin->isActive()) {
      return FALSE;
    }

    $configuration = $source_plugin->toConfiguration();
    $sync_event = new ContentSyncEvent($configuration);
    $this->eventDispatcher->dispatch($sync_event);

    return TRUE;
  }

}
