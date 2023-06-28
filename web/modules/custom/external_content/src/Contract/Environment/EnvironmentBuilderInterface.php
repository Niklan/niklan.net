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

}
