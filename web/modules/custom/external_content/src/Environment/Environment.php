<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\EventListener;
use Drupal\external_content\Data\PrioritizedList;

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
  public function addEventListener(string $class, string $listener, int $priority = 0): EnvironmentBuilderInterface {
    $this->eventListeners->add(new EventListener($class, $listener), $priority);

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

}
