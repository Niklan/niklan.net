<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Event;

use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Event\Event
 * @ingroup external_content
 */
final class EventTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testEvent(): void {
    $event = new FooEvent();
    self::assertFalse($event->isPropagationStopped());

    $event->stopPropagation();
    self::assertTrue($event->isPropagationStopped());
  }

}
