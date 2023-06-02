<?php declare(strict_types = 1);

namespace Drupal\niklan\Event;

use Drupal\external_content\Data\SourceConfiguration;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Provides an event for request content synchronization.
 *
 * @ingroup content_sync
 */
final class ContentSyncEvent extends Event {

  /**
   * Constructs a new ContentSyncEvent instance.
   *
   * @param \Drupal\external_content\Data\SourceConfiguration $configuration
   *   The source content configuration.
   */
  public function __construct(
    protected SourceConfiguration $configuration,
  ) {}

  /**
   * Gets the source configuration.
   *
   * @return \Drupal\external_content\Data\SourceConfiguration
   *   The source configuration.
   */
  public function getSourceConfiguration(): SourceConfiguration {
    return $this->configuration;
  }

}
