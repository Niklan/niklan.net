<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\FinderInterface;
use Drupal\external_content\Contract\GrouperInterface;
use Drupal\external_content\Contract\HtmlParserInterface;
use Drupal\external_content\Contract\MarkupConverterInterface;
use Drupal\external_content\Data\Configuration;
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
   * The list of content groupers.
   */
  protected PrioritizedList $groupers;

  /**
   * The list of markup converters.
   */
  protected PrioritizedList $markupConverters;

  /**
   * The list of finders.
   */
  protected PrioritizedList $finders;

  /**
   * Constructs a new Environment instance.
   *
   * @param \Drupal\external_content\Data\Configuration $configuration
   *   The environment configuration.
   */
  public function __construct(
    protected Configuration $configuration,
  ) {
    $this->htmlParsers = new PrioritizedList();
    $this->groupers = new PrioritizedList();
    $this->markupConverters = new PrioritizedList();
    $this->finders = new PrioritizedList();
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
  public function addGrouper(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, GrouperInterface::class));
    $this->groupers->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addMarkupConverter(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, MarkupConverterInterface::class));
    $this->markupConverters->add($class, $priority);

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
  public function getGroupers(): PrioritizedList {
    return $this->groupers;
  }

  /**
   * {@inheritdoc}
   */
  public function getMarkupConverters(): PrioritizedList {
    return $this->markupConverters;
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

}
