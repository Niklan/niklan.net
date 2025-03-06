<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Pipeline;

use Drupal\external_content\Pipeline\NullContext;
use Drupal\Tests\UnitTestCase;
use Psr\Log\NullLogger;

/**
 * @covers \Drupal\external_content\Pipeline\NullContext
 * @group external_content
 */
final class NullContextTest extends UnitTestCase {

  public function testContext(): void {
    $context = new NullContext();
    self::assertInstanceOf(NullLogger::class, $context->getLogger());
  }

}
