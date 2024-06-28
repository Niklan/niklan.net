<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ContentCollection;
use Drupal\external_content\Node\Content;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content document collection test.
 *
 * @covers \Drupal\external_content\Data\ContentCollection
 * @group external_content
 */
final class ExternalContentDocumentCollectionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $instance = new ContentCollection();

    self::assertCount(0, $instance);
    self::assertEquals([], $instance->getIterator()->getArrayCopy());

    $document = new Content();
    $instance->add($document);

    self::assertCount(1, $instance);
    self::assertEquals([$document], $instance->getIterator()->getArrayCopy());
  }

}
