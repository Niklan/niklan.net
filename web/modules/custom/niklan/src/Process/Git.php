<?php declare(strict_types = 1);

namespace Drupal\niklan\Process;

use Drupal\Core\Site\Settings;
use Symfony\Component\Process\Process;

/**
 * Provides object to work with Git repositories.
 */
final class Git implements GitInterface {

  /**
   * Constructs a new Git object.
   *
   * @param \Drupal\niklan\Process\TerminalInterface $terminal
   *   The terminal process.
   * @param string $gitBinary
   *   The git binary.
   */
  public function __construct(
    protected TerminalInterface $terminal,
    protected string $gitBinary = 'git',
  ) {}

  /**
   * Gets Git binary path.
   */
  protected function getGitBin(): string {
    return Settings::get('niklan_git_binary', $this->gitBinary);
  }

  /**
   * {@inheritdoc}
   */
  public function pull(string $directory): Process {
    // @see https://stackoverflow.com/a/62653400/4751623
    $command = [$this->getGitBin(), 'pull', '--ff-only'];

    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId(string $directory): Process {
    $command = [$this->getGitBin(), 'log', '--format="%H"', '-n 1'];

    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getFileLastCommitId(string $directory, string $filepath): Process {
    $command = [
      $this->getGitBin(),
      'log',
      '--format="%H"',
      '-n 1',
      '--',
      $filepath,
    ];

    return $this->terminal->createProcess($command, $directory);
  }

}
