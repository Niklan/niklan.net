<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Exception;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingSerializerException;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Exception\MissingSerializerException
 * @ingroup external_content
 */
final class MissingSerializerExceptionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testException(): void {
    $available = $this->prophesize(NodeSerializerInterface::class);
    $available = $available->reveal();

    $node = $this->prophesize(NodeInterface::class);
    $node = $node->reveal();

    $environment = new Environment();
    $environment->addSerializer($available);

    $exception = new MissingSerializerException(
      node: $node,
      environment: $environment,
    );

    self::assertSame($node, $exception->node);
    self::assertStringContainsString(
      $node::class,
      $exception->getMessage(),
    );
    self::assertStringContainsString(
      $available::class,
      $exception->getMessage(),
    );
  }

}
