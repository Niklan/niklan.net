<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\EventListener;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for event listener.
 *
 * @covers \Drupal\external_content\Data\EventListener
 * @group external_content
 */
final class EventListenerTest extends UnitTestCase {

  public function testObject(): void {
    $callback = static fn () => 'bar';
    $event = new EventListener('foo', $callback);

    self::assertEquals('foo', $event->getEvent());
    self::assertEquals($callback, $event->getListener());
  }

}
