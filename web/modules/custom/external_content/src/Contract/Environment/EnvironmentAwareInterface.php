<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

/**
 * Represents a class tha requires external content environment.
 */
interface EnvironmentAwareInterface {

  /**
   * Sets the environment.
   *
   * @param \Drupal\external_content\Contract\Environment\EnvironmentInterface $environment
   *   The environment instance.
   */
  public function setEnvironment(EnvironmentInterface $environment): void;

  /**
   * Gets the environment.
   *
   * @return \Drupal\external_content\Contract\Environment\EnvironmentInterface
   *   The environment instance.
   */
  public function getEnvironment(): EnvironmentInterface;

}
