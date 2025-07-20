<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Process;

use Drupal\niklan\Console\Process\Git;
use Drupal\niklan\Console\Process\GitInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Git::class)]
final class GitTest extends NiklanTestBase {

  /**
   * The git process.
   */
  protected GitInterface $git;

  /**
   * Tests process creation for pulling from repository.
   */
  public function testPull(): void {
    $process = $this->git->pull('/foo/bar');
    $this->assertEquals("'git' 'pull' '--ff-only'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

  /**
   * Tests process creation for getting last commit ID.
   */
  public function testGetLastCommitId(): void {
    $process = $this->git->getLastCommitId('/foo/bar');
    $this->assertEquals("'git' 'log' '--format=\"%H\"' '-n 1'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

  /**
   * Tests process creation for getting last commit ID of the file.
   */
  public function testGetFileLastCommitId(): void {
    $process = $this->git->getFileLastCommitId('/foo/bar', 'baz/index.md');
    $this->assertEquals("'git' 'log' '--format=\"%H\"' '-n 1' '--' 'baz/index.md'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->git = $this->container->get(GitInterface::class);
  }

}
