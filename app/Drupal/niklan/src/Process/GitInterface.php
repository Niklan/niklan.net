<?php

declare(strict_types=1);

namespace Drupal\niklan\Process;

use Symfony\Component\Process\Process;

/**
 * Provides interface for git interaction classes.
 */
interface GitInterface {

  /**
   * Calls 'git pull' in provided directory.
   *
   * @param string $directory
   *   The working repository directory.
   */
  public function pull(string $directory): Process;

  /**
   * Gets the last commit ID in repository.
   *
   * @param string $directory
   *   The working repository directory.
   */
  public function getLastCommitId(string $directory): Process;

  /**
   * Gets the last commit ID for specific file in repository.
   *
   * @param string $directory
   *   The working repository directory.
   * @param string $filepath
   *   The relative filepath.
   */
  public function getFileLastCommitId(string $directory, string $filepath): Process;

}
