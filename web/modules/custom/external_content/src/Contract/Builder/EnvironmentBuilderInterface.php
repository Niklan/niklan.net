<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Represents an interface for environment builder.
 */
interface EnvironmentBuilderInterface {

  /**
   * {@selfdoc}
   */
  public function addHtmlParser(string $class, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addBundler(string $class, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addFinder(string $class, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addBuilder(string $class, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addSerializer(string $class, int $priority): self;

  /**
   * {@selfdoc}
   */
  public function addEventListener(string $event_class, callable $listener, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self;

}
