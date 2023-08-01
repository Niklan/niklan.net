<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Represents an interface for environment builder.
 */
interface EnvironmentBuilderInterface {

  /**
   * Adds an HTML parser into environment.
   *
   * @param string $class
   *   The FQN of HTML parser.
   * @param int $priority
   *   The priority of the parser.
   */
  public function addHtmlParser(string $class, int $priority = 0): self;

  /**
   * Adds a content bundler into environment.
   *
   * @param string $class
   *   The FQN of content bundler.
   * @param int $priority
   *   The priority of the bundler.
   */
  public function addBundler(string $class, int $priority = 0): self;

  /**
   * Adds a finder into environment.
   *
   * @param string $class
   *   The FQN of a finder.
   * @param int $priority
   *   The priority of the finder.
   */
  public function addFinder(string $class, int $priority = 0): self;

  /**
   * Adds a builder into environment.
   *
   * @param string $class
   *   The FQN of a builder.
   * @param int $priority
   *   The priority of the finder.
   */
  public function addBuilder(string $class, int $priority = 0): self;

  /**
   * Adds event listener for environment.
   *
   * @param string $event_class
   *   The FQN of the event class to subscribe to.
   * @param callable $listener
   *   The listener to execute.
   * @param int $priority
   *   The priority of listener.
   *
   * @return $this
   */
  public function addEventListener(string $event_class, callable $listener, int $priority = 0): self;

  /**
   * Sets the event dispatcher for environment.
   *
   * @param \Psr\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   *
   * @return $this
   */
  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self;

}
