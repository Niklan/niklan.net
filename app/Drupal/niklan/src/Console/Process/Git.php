<?php

declare(strict_types=1);

namespace Drupal\niklan\Console\Process;

use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Process;

final class Git implements GitInterface {

  public function __construct(
    #[Autowire(service: 'niklan.process.terminal')]
    protected TerminalInterface $terminal,
    protected string $gitBinary = 'git',
  ) {}

  #[\Override]
  public function pull(string $directory): Process {
    // @see https://stackoverflow.com/a/62653400/4751623
    $command = [$this->getGitBin(), 'pull', '--ff-only'];

    return $this->terminal->createProcess($command, $directory);
  }

  #[\Override]
  public function getLastCommitId(string $directory): Process {
    $command = [$this->getGitBin(), 'log', '--format="%H"', '-n 1'];

    return $this->terminal->createProcess($command, $directory);
  }

  #[\Override]
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

  #[\Override]
  public function describeTags(string $directory): Process {
    $command = [
      $this->getGitBin(),
      'describe',
      '--tags',
    ];

    return $this->terminal->createProcess($command, $directory);
  }

  protected function getGitBin(): string {
    return Settings::get('niklan_git_binary', $this->gitBinary);
  }

}
