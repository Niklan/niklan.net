<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\FileFinderExtension;
use Drupal\Tests\UnitTestCase;

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
    $environment = new Environment();
    self::assertCount(0, $environment->getFinders());

    $environment->addExtension(new FileFinderExtension());
    self::assertCount(1, $environment->getFinders());
  }

}
