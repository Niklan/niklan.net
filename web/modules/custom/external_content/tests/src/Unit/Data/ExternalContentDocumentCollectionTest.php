<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ExternalContentDocumentCollection;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content document collection test.
 *
 * @covers \Drupal\external_content\Data\ExternalContentDocumentCollection
 * @group external_content
 */
final class ExternalContentDocumentCollectionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $instance = new ExternalContentDocumentCollection();

    self::assertCount(0, $instance);
    self::assertEquals([], $instance->getIterator()->getArrayCopy());

    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);
    $instance->add($document);

    self::assertCount(1, $instance);
    self::assertEquals([$document], $instance->getIterator()->getArrayCopy());
  }

}
