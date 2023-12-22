<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit;

use Drupal\external_content\Exception\MissingContainerException;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Exception\MissingContainerException
 * @ingroup external_content
 */
final class MissingContainerExceptionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testException(): void {
    $class = $this->prophesize(ContainerAwareInterface::class);
    $class = $class->reveal();

    $exception = new MissingContainerException($class::class);
    self::assertSame($class::class, $exception->containerAwareClass);
    self::assertStringContainsString($class::class, $exception->getMessage());
  }

}
