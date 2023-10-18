<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\ExternalContentBundleDocument;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content bundle document test.
 *
 * @covers \Drupal\external_content\Data\ExternalContentBundleDocument
 * @group external_content
 */
final class ExternalContentBundleDocumentTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);
    $attributes = new Attributes();

    $instance = new ExternalContentBundleDocument($document, $attributes);

    self::assertEquals($document, $instance->getDocument());
    self::assertEquals($attributes, $instance->getAttributes());

    // Without attribute.
    $instance = new ExternalContentBundleDocument($document);
    self::assertEquals($document, $instance->getDocument());
    self::assertInstanceOf(Attributes::class, $instance->getAttributes());
  }

}