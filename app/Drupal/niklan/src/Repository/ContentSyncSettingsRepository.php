<?php declare(strict_types = 1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

/**
 * Provides content sync settings repository.
 *
 * @ingroup content_sync
 */
final class ContentSyncSettingsRepository implements ContentSyncSettingsRepositoryInterface {

  /**
   * Constructs a new ContentSyncSettingsRepository instance.
   *
   * @param \Drupal\Core\KeyValueStore\KeyValueStoreInterface $store
   *   The key/value store.
   */
  public function __construct(
    protected KeyValueStoreInterface $store,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function setWorkingDir(?string $working_dir): self {
    if (!$working_dir) {
      $this->store->delete('working_dir');
    }
    else {
      $this->store->set('working_dir', $working_dir);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWorkingDir(): ?string {
    return $this->store->get('working_dir');
  }

}
