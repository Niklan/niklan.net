<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

/**
 * {@selfdoc}
 */
interface EnvironmentManagerInterface {

  /**
   * {@selfdoc}
   */
  public function get(string $environment_id): EnvironmentInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $environment_id): bool;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *   label: string,
   *   id: string,
   *   }
   */
  public function list(): array;

}
