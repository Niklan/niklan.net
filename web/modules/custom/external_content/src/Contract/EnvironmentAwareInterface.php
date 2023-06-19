<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Represents a class tha requires external content environment.
 */
interface EnvironmentAwareInterface {

  /**
   * Sets the environment.
   *
   * @param \Drupal\external_content\Contract\EnvironmentInterface $environment
   *   The environment instance.
   *
   * @return $this
   */
  public function setEnvironment(EnvironmentInterface $environment): self;

  /**
   * Gets the environment.
   *
   * @return \Drupal\external_content\Contract\EnvironmentInterface
   *   The environment instance.
   */
  public function getEnvironment(): EnvironmentInterface;

}
