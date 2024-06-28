<?php

declare(strict_types=1);

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
   * @param \Closure $listener
   *   The listener closure.
   */
  public function __construct(
    protected string $event,
    protected \Closure $listener,
  ) {}

  /**
   * Gets FQN of the event.
   */
  public function getEvent(): string {
    return $this->event;
  }

  /**
   * Gets the event callable.
   */
  public function getListener(): \Closure {
    return $this->listener;
  }

}
