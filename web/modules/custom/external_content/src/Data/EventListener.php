<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a value object for event listener.
 */
final class EventListener {

  /**
   * Constructs a new EventListener instance.
   *
   * @param string $event
   *   The FQN of listened event.
   * @param string $listener
   *   The FQN of listener.
   */
  public function __construct(
    protected string $event,
    protected string $listener,
  ) {}

  /**
   * Gets FQN of the event.
   */
  public function getEvent(): string {
    return $this->event;
  }

  /**
   * Gets FQN of the event listener.
   */
  public function getListener(): string {
    return $this->listener;
  }

}
