<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

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
   *
   * @return $this
   */
  public function addHtmlParser(string $class, int $priority = 0): self;

  /**
   * Adds a content grouper into environment.
   *
   * @param string $class
   *   The FQN of content grouper.
   * @param int $priority
   *   The priority of the grouper.
   *
   * @return $this
   */
  public function addGrouper(string $class, int $priority = 0): self;

  /**
   * Adds a markup converter into environment.
   *
   * @param string $class
   *   The FQN of markup converter.
   * @param int $priority
   *   The priority of the markup converter.
   *
   * @return $this
   */
  public function addMarkupConverter(string $class, int $priority = 0): self;

  /**
   * Adds a finder into environment.
   *
   * @param string $class
   *   The FQN of a finder.
   * @param int $priority
   *   The priority of the finder.
   *
   * @return $this
   */
  public function addFinder(string $class, int $priority = 0): self;

}
