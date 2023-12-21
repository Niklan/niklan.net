<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Grouper;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\ParsedSourceFileCollection;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Grouper\ParsedSourceFileGrouperInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a parsed source file grouper test.
 *
 * @coversDefaultClass \Drupal\external_content\Grouper\ParsedSourceFileGrouper
 */
final class ParsedSourceFileGrouperTest extends ExternalContentTestBase {

  /**
   * The parsed source file grouper.
   */
  protected ?ParsedSourceFileGrouperInterface $grouper;

  /**
   * Tests that grouper works as expected.
   */
  public function testDefaultGrouper(): void {
    $file_a = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'a', 'language' => 'en']),
      new SourceFileContent(),
    );

    $file_b = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'a', 'language' => 'ru']),
      new SourceFileContent(),
    );

    $file_collection = new ParsedSourceFileCollection();
    $file_collection->add($file_a);
    $file_collection->add($file_b);

    $result = $this->grouper->group($file_collection);
    self::assertInstanceOf(ExternalContentCollection::class, $result);
    self::assertCount(1, $result);
    $result_a = $result->get('a');
    self::assertTrue($result_a->hasTranslation('en'));
    self::assertTrue($result_a->hasTranslation('ru'));
  }

  /**
   * Tests behavior when called with non existed grouper.
   */
  public function testNotExistedGrouper(): void {
    $collection = new ParsedSourceFileCollection();
    self::expectException(PluginNotFoundException::class);
    $this->grouper->group($collection, 'foo-bar');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->grouper = $this->container->get(
      ParsedSourceFileGrouperInterface::class,
    );
  }

}
