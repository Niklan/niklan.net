<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Environment;

interface EnvironmentManagerInterface {

  public function get(string $environment_id): EnvironmentInterface;

  public function has(string $environment_id): bool;

  /**
   * @return array{
   *   service: string,
   *   label: string,
   *   id: string,
   *   }
   */
  public function list(): array;

}
