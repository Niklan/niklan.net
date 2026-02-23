<?php

declare(strict_types=1);

namespace Drupal\Tests\app_platform\Kernel\Process;

use Drupal\app_contract\Contract\Console\Git;
use Drupal\app_platform\Console\Process\ProcessGit;
use Drupal\Tests\app_platform\Kernel\AppPlatformTestBase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ProcessGit::class)]
final class GitTest extends AppPlatformTestBase {

  /**
   * The git process.
   */
  protected Git $git;

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
    $this->git = $this->container->get(Git::class);
  }

}
