<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Event;

use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for HTML pre parse event.
 *
 * @covers \Drupal\external_content\Event\HtmlPreParseEvent
 * @group external_content
 */
final class HtmlPreParseEventTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testEvent(): void {
    $file = new ExternalContentFile('foo', 'bar');
    $html = new ExternalContentHtml($file, 'foo');

    $event = new HtmlPreParseEvent($html);

    self::assertEquals($html, $event->getHtml());
  }

}
