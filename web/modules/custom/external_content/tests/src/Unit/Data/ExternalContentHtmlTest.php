<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content HTML.
 *
 * @covers \Drupal\external_content\Data\ExternalContentHtml
 * @group external_content
 */
final class ExternalContentHtmlTest extends UnitTestCase {

  /**
   * Tests the object.
   */
  public function testObject(): void {
    $html = '<p>Hello, World!</p>';
    $file = new ExternalContentFile('foo', 'bar');
    $instance = new ExternalContentHtml($file, $html);

    self::assertEquals($file, $instance->getFile());
    self::assertEquals($html, $instance->getContent());

    $html_b = '<p>Hello, <strong>World</strong>!';
    $instance->setContent($html_b);

    self::assertEquals($html_b, $instance->getContent());
  }

}
