<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Provides a base event implementation.
 */
abstract class Event implements StoppableEventInterface {

  /**
   * Indicates whether propagation should be stopped.
   */
  protected bool $propagationStopped = FALSE;

  /**
   * {@inheritdoc}
   */
  final public function isPropagationStopped(): bool {
    return $this->propagationStopped;
  }

  /**
   * Stops the propagation of the event to further event listeners.
   */
  final public function stopPropagation(): void {
    $this->propagationStopped = TRUE;
  }

}
