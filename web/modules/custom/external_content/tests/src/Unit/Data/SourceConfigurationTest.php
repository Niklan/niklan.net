<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\SourceConfiguration;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for source configuration DTO.
 *
 * @coversDefaultClass \Drupal\external_content\Data\SourceConfiguration
 */
final class SourceConfigurationTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   *
   * @dataProvider testData
   */
  public function testClass(string $working_dir, string $grouping_plugin_id, ?string $source_plugin_id): void {
    $instance = new SourceConfiguration(
      $working_dir,
      $grouping_plugin_id,
      $source_plugin_id,
    );

    self::assertEquals($working_dir, $instance->getWorkingDir());
    self::assertEquals($grouping_plugin_id, $instance->getGroupingPluginId());
    self::assertEquals($source_plugin_id, $instance->getSourcePluginId());
  }

  /**
   * Provides testing data.
   *
   * @return \Generator
   *   The data for testing.
   */
  public function testData(): \Generator {
    yield 'without source plugin id' => [
      'public://working-dir',
      'front_matter',
      NULL,
    ];

    yield 'with source plugin id' => [
      'public://working-dir',
      'front_matter',
      'source_plugin',
    ];
  }

}
