<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Event;

use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Event\HtmlPostParseEvent;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for HTML post parse event.
 *
 * @covers \Drupal\external_content\Event\HtmlPostParseEvent
 * @group external_content
 */
final class HtmlPostParseEventTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testEvent(): void {
    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);

    $event = new HtmlPostParseEvent($document);

    self::assertEquals($event->getHtml(), $document);
  }

}
