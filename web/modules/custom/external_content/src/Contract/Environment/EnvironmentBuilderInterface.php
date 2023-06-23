<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

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
   * Adds a content grouper into environment.
   *
   * @param string $class
   *   The FQN of content grouper.
   * @param int $priority
   *   The priority of the grouper.
   */
  public function addGrouper(string $class, int $priority = 0): self;

  /**
   * Adds a markup converter into environment.
   *
   * @param string $class
   *   The FQN of markup converter.
   * @param int $priority
   *   The priority of the markup converter.
   */
  public function addMarkupConverter(string $class, int $priority = 0): self;

  /**
   * Adds a markup pre-converter into environment.
   *
   * @param string $class
   *   The FQN of markup pre-converter.
   * @param int $priority
   *   The priority of the markup pre-converter.
   */
  public function addMarkupPreConverter(string $class, int $priority = 0): self;

  /**
   * Adds a markup post-converter into environment.
   *
   * @param string $class
   *   The FQN of markup post-converter.
   * @param int $priority
   *   The priority of the markup post-converter.
   */
  public function addMarkupPostConverter(string $class, int $priority = 0): self;

  /**
   * Adds a finder into environment.
   *
   * @param string $class
   *   The FQN of a finder.
   * @param int $priority
   *   The priority of the finder.
   */
  public function addFinder(string $class, int $priority = 0): self;

}
