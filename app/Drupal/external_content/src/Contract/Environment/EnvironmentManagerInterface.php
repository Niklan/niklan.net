<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

/**
 * {@selfdoc}
 */
interface EnvironmentManagerInterface {

  /**
   * {@selfdoc}
   */
  public function getEnvironment(string $environment_id): EnvironmentInterface;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *   label: string,
   *   id: string,
   *   }
   */
  public function getEnvironments(): array;

}
