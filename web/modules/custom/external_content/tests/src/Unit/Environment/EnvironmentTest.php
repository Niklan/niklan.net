<?php declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Environment;

use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCaseTest;

/**
 * Provides a test for environment.
 *
 * @ingroup external_content
 * @coversDefaultClass \Drupal\external_content\Environment\Environment
 */
final class EnvironmentTest extends UnitTestCaseTest {

  /**
   * Tests that event system works as expected.
   */
  public function testEvents(): void {
    $event = new FooEvent();
    $configuration = new Configuration();
    $environment = new Environment($configuration);

    self::assertFalse($environment->getListenersForEvent($event)->valid());

    $listener_called = FALSE;
    $listener = static function () use (&$listener_called) {
      $listener_called = TRUE;
    };

    $environment->addEventListener(FooEvent::class, $listener);
    self::assertTrue($environment->getListenersForEvent($event)->valid());
    self::assertFalse($listener_called);

    $environment->dispatch($event);
    self::assertTrue($listener_called);
  }

}
