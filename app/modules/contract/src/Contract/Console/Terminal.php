<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Console;

use Symfony\Component\Process\Process;

/**
 * Defines an interface for terminal process creation.
 */
interface Terminal {

  /**
   * Creates new terminal process.
   *
   * @param array $command
   *   The command to run and its arguments listed as separate entries.
   * @param string|null $cwd
   *   The working directory or null to use the working dir of the current PHP
   *   process.
   * @param array|null $env
   *   The environment variables or null to use the same environment as the
   *   current PHP process.
   * @param mixed $input
   *   The input as stream resource, scalar or \Traversable, or null for no
   *    input.
   * @param float|null $timeout
   *   The timeout in seconds or null to disable.
   */
  public function createProcess(array $command, ?string $cwd = NULL, ?array $env = NULL, mixed $input = NULL, ?float $timeout = 60): Process;

}
