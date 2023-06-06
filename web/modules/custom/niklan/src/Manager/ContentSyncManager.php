<?php declare(strict_types = 1);

namespace Drupal\niklan\Manager;

use Drupal\external_content\Contract\SourcePluginInterface;
use Drupal\external_content\Contract\SourcePluginManagerInterface;

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
   */
  public function __construct(
    protected SourcePluginManagerInterface $sourcePluginManager,
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

    return TRUE;
  }

}
