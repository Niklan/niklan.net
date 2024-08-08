<?php

declare(strict_types=1);

namespace Drupal\niklan\Process;

use Symfony\Component\Process\Process;

interface GitInterface {

  public function pull(string $directory): Process;

  public function getLastCommitId(string $directory): Process;

  public function getFileLastCommitId(string $directory, string $filepath): Process;

  /**
   * Gets the last available tag from the repository.
   *
   * @param string $directory
   *   The working repository directory.
   */
  public function describeTags(string $directory): Process;

}
