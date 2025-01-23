<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Environment;

interface EnvironmentManagerInterface {

  public function get(string $environment_id): EnvironmentInterface;

  public function has(string $environment_id): bool;

  /**
   * @return array<string, array{service: string, id: string, label: string}>
   */
  public function list(): array;

}
