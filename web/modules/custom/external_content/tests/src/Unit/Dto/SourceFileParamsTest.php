<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\SourceFileParams;
use Drupal\Tests\UnitTestCase;

/**
 * Validates that source params value object works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\SourceFileParams
 */
final class SourceFileParamsTest extends UnitTestCase {

  /**
   * Tests class behavior.
   */
  public function testClass(): void {
    $params = [
      'foo' => [
        'bar' => 'baz',
      ],
      'boo' => 'foo',
    ];

    $source_params = new SourceFileParams($params);

    $this->assertEquals($params, $source_params->all());
    $this->assertTrue($source_params->has('foo'));
    $this->assertTrue($source_params->has('foo.bar'));
    $this->assertTrue($source_params->has('boo'));
    $this->assertFalse($source_params->has('baz'));

    $this->assertEquals(['bar' => 'baz'], $source_params->get('foo'));
    $this->assertEquals('baz', $source_params->get('foo.bar'));
    $this->assertEquals('foo', $source_params->get('boo'));
    $this->assertNull($source_params->get('baz'));
  }

}
