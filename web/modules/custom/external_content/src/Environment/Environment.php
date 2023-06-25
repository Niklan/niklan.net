<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Converter\MarkupConverterInterface;
use Drupal\external_content\Contract\Converter\MarkupPostConverterInterface;
use Drupal\external_content\Contract\Converter\MarkupPreConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
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
   * The list of content bundlers.
   */
  protected PrioritizedList $bundlers;

  /**
   * The list of markup converters.
   */
  protected PrioritizedList $markupConverters;

  /**
   * The list of markup pre-converters.
   */
  protected PrioritizedList $markupPreConverters;

  /**
   * The list of markup post-converters.
   */
  protected PrioritizedList $markupPostConverters;

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
    $this->bundlers = new PrioritizedList();
    $this->markupConverters = new PrioritizedList();
    $this->markupPreConverters = new PrioritizedList();
    $this->markupPostConverters = new PrioritizedList();
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
  public function addBundler(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, BundlerInterface::class));
    $this->bundlers->add($class, $priority);

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
  public function getBundlers(): PrioritizedList {
    return $this->bundlers;
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

  /**
   * {@inheritdoc}
   */
  public function addMarkupPreConverter(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, MarkupPreConverterInterface::class));
    $this->markupPreConverters->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addMarkupPostConverter(string $class, int $priority = 0): EnvironmentBuilderInterface {
    \assert(\is_subclass_of($class, MarkupPostConverterInterface::class));
    $this->markupPostConverters->add($class, $priority);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMarkupPreConverters(): PrioritizedList {
    return $this->markupPreConverters;
  }

  /**
   * {@inheritdoc}
   */
  public function getMarkupPostConverters(): PrioritizedList {
    return $this->markupPostConverters;
  }

}
