<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\SourceFileMetadata;
use Drupal\Tests\UnitTestCase;

/**
 * Validates that source metadata value object works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\SourceFileMetadata
 */
final class SourceMetadataTest extends UnitTestCase {

  /**
   * Tests class behavior.
   */
  public function testClass(): void {
    $metadata = [
      'foo' => [
        'bar' => 'baz',
      ],
      'boo' => 'foo',
    ];

    $source_metadata = new SourceFileMetadata($metadata);

    $this->assertEquals($metadata, $source_metadata->all());
    $this->assertTrue($source_metadata->has('foo'));
    $this->assertTrue($source_metadata->has('foo.bar'));
    $this->assertTrue($source_metadata->has('boo'));
    $this->assertFalse($source_metadata->has('baz'));

    $this->assertEquals(['bar' => 'baz'], $source_metadata->get('foo'));
    $this->assertEquals('baz', $source_metadata->get('foo.bar'));
    $this->assertEquals('foo', $source_metadata->get('boo'));
    $this->assertNull($source_metadata->get('baz'));
  }

}
