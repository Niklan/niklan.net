<?php

declare(strict_types=1);

namespace Drupal\niklan\Console\Process;

use Drupal\app_contract\Contract\Console\Terminal;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\Process\Process;

/**
 * Provides terminal for CLI interaction.
 *
 * This class is wrapper around Symfony's Process component. This is done
 * because code that uses it should be tested, but the Process can't be easily
 * mocked directly. This service should be used to work with that component, in
 * that case it can be easily mocked via this service.
 *
 * @see https://symfony.com/doc/current/components/process.html
 */
final class ProcessTerminal implements Terminal {

  public function __construct(
    protected FileSystemInterface $fileSystem,
  ) {}

  #[\Override]
  public function createProcess(array $command, ?string $cwd = NULL, ?array $env = NULL, mixed $input = NULL, ?float $timeout = 60): Process {
    if ($cwd) {
      $cwd = $this->realpath($cwd);
    }

    return new Process($command, $cwd, $env, $input, $timeout);
  }

  /**
   * Get directory realpath.
   *
   * Symfony's Process doesn't aware about Stream Wrappers. This leads to
   * process run fails when using schemes like 'public://'. This fix it by
   * preprocess directory before it passed to Process.
   */
  protected function realpath(string $directory): string {
    $realpath = $this->fileSystem->realpath($directory);

    // Fallback to default value if realpath failed. Let Process to handle that.
    if (!$realpath) {
      $realpath = $directory;
    }

    return $realpath;
  }

}
