<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Process;

use Drupal\Core\File\FileSystemInterface;
use Drupal\niklan\Console\Process\Terminal;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Process\Process;

/**
 * Provides test for terminal wrapper.
 *
 * @coversDefaultClass \Drupal\niklan\Console\Process\Terminal
 */
final class TerminalTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that object is properly creates real Process instance.
   */
  public function testObject(): void {
    $file_system = $this->prophesize(FileSystemInterface::class);
    $file_system->realpath(Argument::any())->will(static fn ($args) => $args[0]);

    $terminal = new Terminal($file_system->reveal());
    $result = $terminal->createProcess(['pwd']);
    self::assertInstanceOf(Process::class, $result);
  }

}
