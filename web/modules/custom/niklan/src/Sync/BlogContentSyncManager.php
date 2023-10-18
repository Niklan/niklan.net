<?php declare(strict_types = 1);

namespace Drupal\niklan\Sync;

use Drupal\external_content\Contract\Finder\FinderFacadeInterface;
use Drupal\niklan\Builder\BlogContentEnvironmentBuilder;

/**
 * Provides content synchronization manager.
 *
 * @ingroup content_sync
 */
final class BlogContentSyncManager {

  /**
   * Constructs a new BlogContentSyncManager instance.
   */
  public function __construct(
    private BlogContentEnvironmentBuilder $environmentBuilder,
    private FinderFacadeInterface $finder,
  ) {}

  /**
   * Requests content synchronization.
   */
  public function synchronize(): void {
    $this->finder->setEnvironment($this->environmentBuilder->build());
    $this->finder->find();
    // @todo Build Queue and run it.
  }

}
