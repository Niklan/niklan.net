<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Exception;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingDeserializerException;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Exception\MissingDeserializerException
 * @ingroup external_content
 */
final class MissingDeserializerExceptionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testException(): void {
    $available = $this->prophesize(SerializerInterface::class);
    $available = $available->reveal();

    $environment = new Environment();
    $environment->addSerializer($available);

    $type = $this->randomString();
    $version = $this->randomString();

    $exception = new MissingDeserializerException(
      type: $type,
      version: $version,
      environment: $environment,
    );

    self::assertSame($type, $exception->type);
    self::assertSame($version, $exception->version);
    self::assertStringContainsString(
      $available::class,
      $exception->getMessage(),
    );
  }

}
