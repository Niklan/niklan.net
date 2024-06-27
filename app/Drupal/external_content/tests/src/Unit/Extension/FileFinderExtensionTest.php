<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\FileFinderExtension;
use Drupal\external_content\Finder\FileFinder;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Extension\FileFinderExtension
 * @group external_content
 */
final class FileFinderExtensionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testExtension(): void {
    $environment = new Environment('test');
    self::assertCount(0, $environment->getFinders());

    $mime_guesser = $this->prophesize(MimeTypeGuesserInterface::class);
    $mime_guesser = $mime_guesser->reveal();
    $file_finder = new FileFinder($mime_guesser);
    $environment->addExtension(new FileFinderExtension($file_finder));
    self::assertCount(1, $environment->getFinders());
    self::assertSame(
      expected: $file_finder,
      actual: $environment->getFinders()->getIterator()->current(),
    );
  }

}
