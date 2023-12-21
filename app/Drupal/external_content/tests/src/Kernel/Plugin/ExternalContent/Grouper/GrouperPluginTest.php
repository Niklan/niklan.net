<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Grouper;

use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\ParsedSourceFileCollection;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperPluginManagerInterface;
use Drupal\external_content_test\Plugin\ExternalContent\Grouper\FalseGrouper;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for grouper plugin base functionality.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperPlugin
 */
final class GrouperPluginTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The grouper plugin manager.
   */
  protected ?GrouperPluginManagerInterface $grouperPluginManager = NULL;

  /**
   * Tests that grouper works properly when no valid data is provided.
   */
  public function testGrouperWithoutApplicableData(): void {
    $file = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'a', 'language' => 'ru']),
      new SourceFileContent(),
    );

    $file_collection = new ParsedSourceFileCollection();
    $file_collection->add($file);

    $false_grouper = $this->grouperPluginManager->createInstance('false');
    \assert($false_grouper instanceof FalseGrouper);

    $result_collection = $false_grouper->group($file_collection);
    self::assertEquals(0, $result_collection->getIterator()->count());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->grouperPluginManager = $this
      ->container
      ->get(GrouperPluginManagerInterface::class);
  }

}
