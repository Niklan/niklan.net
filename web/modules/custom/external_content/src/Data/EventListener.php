<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a value object for event listener.
 */
final class EventListener {

  /**
   * The event listener.
   *
   * @var callable
   */
  protected $listener;

  /**
   * Constructs a new EventListener instance.
   *
   * @param string $event
   *   The FQN of listened event.
   * @param callable $listener
   *   The FQN of listener.
   */
  public function __construct(
    protected string $event,
    callable $listener,
  ) {
    $this->listener = $listener;
  }

  /**
   * Gets FQN of the event.
   */
  public function getEvent(): string {
    return $this->event;
  }

  /**
   * Gets the event callable.
   */
  public function getListener(): callable {
    return $this->listener;
  }

}
