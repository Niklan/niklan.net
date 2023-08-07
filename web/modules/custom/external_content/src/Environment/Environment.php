<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\EventListener;
use Drupal\external_content\Data\PrioritizedList;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Provides an environment for a specific external content processing.
 */
final class Environment implements EnvironmentInterface, EnvironmentBuilderInterface {

  /**
   * The list of HTML parsers.
   */
  protected PrioritizedList $htmlParsers;

  /**
   * The list of content bundlers.
   */
  protected PrioritizedList $bundlers;

  /**
   * The list of finders.
   */
  protected PrioritizedList $finders;

  /**
   * The event listeners.
   */
  protected PrioritizedList $eventListeners;

  /**
   * The list of builders.
   */
  protected PrioritizedList $builders;

  /**
   * The event dispatcher.
   */
  protected ?EventDispatcherInterface $eventDispatcher = NULL;

  /**
   * Constructs a new Environment instance.
   *
   * @param \Drupal\external_content\Data\Configuration $configuration
   *   The environment configuration.
   */
  public function __construct(
    protected Configuration $configuration,
  ) {
    $this->finders = new PrioritizedList();
    $this->htmlParsers = new PrioritizedList();
    $this->bundlers = new PrioritizedList();
    $this->builders = new PrioritizedList();
    $this->eventListeners = new PrioritizedList();
  }

  /**
   * {@inheritdoc}
   */
  public function addHtmlParser(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, HtmlParserInterface::class));
    $this->htmlParsers->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addBundler(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, BundlerInterface::class));
    $this->bundlers->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHtmlParsers(): PrioritizedList {
    return $this->htmlParsers;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundlers(): PrioritizedList {
    return $this->bundlers;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): Configuration {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function addFinder(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, FinderInterface::class));
    $this->finders->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFinders(): PrioritizedList {
    return $this->finders;
  }

  /**
   * {@inheritdoc}
   */
  public function addEventListener(string $event_class, callable $listener, int $priority = 0): EnvironmentBuilderInterface {
    $this->eventListeners->add(new EventListener($event_class, $listener), $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self {
    $this->eventDispatcher = $event_dispatcher;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addBuilder(string $class, int $priority = 0): EnvironmentBuilderInterface {
    $this->builders->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBuilders(): PrioritizedList {
    return $this->builders;
  }

  /**
   * {@inheritdoc}
   */
  public function dispatch(object $event): object {
    if ($this->eventDispatcher) {
      return $this->eventDispatcher->dispatch($event);
    }

    foreach ($this->getListenersForEvent($event) as $event_listener) {
      if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
        return $event;
      }

      $event_listener($event);
    }

    return $event;
  }

  /**
   * {@inheritdoc}
   */
  public function getListenersForEvent(object $event): \Generator {
    foreach ($this->eventListeners as $event_listener) {
      \assert($event_listener instanceof EventListener);

      if (!\is_a($event, $event_listener->getEvent())) {
        continue;
      }

      yield static fn (object $event) => \call_user_func(
        $event_listener->getListener(),
        $event,
      );
    }
  }

}
