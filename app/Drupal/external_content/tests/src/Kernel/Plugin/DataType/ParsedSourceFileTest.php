<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\DataType;

use Drupal\Core\TypedData\TraversableTypedDataInterface;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Plugin\DataType\ParsedSourceFile as ParsedSourceFileDataType;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for parsed source file data type.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\DataType\ParsedSourceFile
 */
final class ParsedSourceFileTest extends ExternalContentTestBase {

  use ProphecyTrait;

  /**
   * The typed data manager.
   */
  protected ?TypedDataManagerInterface $typedDataManager;

  /**
   * Tests that data type works as expected.
   */
  public function testDataType(): void {
    $definition = $this
      ->typedDataManager
      ->createDataDefinition('external_content_parsed_source_file');
    $typed_data = $this->typedDataManager->create($definition);
    \assert($typed_data instanceof ParsedSourceFileDataType);
    self::assertNull($typed_data->getParsedSourceFile());

    $typed_data->setValue('foo-bar');
    self::assertNull($typed_data->getParsedSourceFile());

    $parsed_source_file = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams([]),
      new SourceFileContent(),
    );
    $typed_data->setValue($parsed_source_file);
    self::assertEquals(
      $parsed_source_file,
      $typed_data->getParsedSourceFile(),
    );
  }

  /**
   * Tests that parent notified if requested.
   */
  public function testParentNotify(): void {
    $is_parent_notified = FALSE;
    $notified_parent_name = NULL;
    $parent = $this->prophesize(TraversableTypedDataInterface::class);
    $parent
      ->onChange(Argument::any())
      ->will(static function ($args) use (&$is_parent_notified, &$notified_parent_name): void {
        $is_parent_notified = TRUE;
        $notified_parent_name = $args[0];
      });

    $parsed_source_file = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams([]),
      new SourceFileContent(),
    );

    $definition = $this
      ->typedDataManager
      ->createDataDefinition('external_content_parsed_source_file');
    $typed_data = $this
      ->typedDataManager
      ->create(
        $definition,
        $parsed_source_file,
        'test_name',
        $parent->reveal(),
      );
    \assert($typed_data instanceof ParsedSourceFileDataType);

    $typed_data->setValue($parsed_source_file);
    self::assertTrue($is_parent_notified);
    self::assertEquals('test_name', $notified_parent_name);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->typedDataManager = $this->container->get('typed_data_manager');
  }

}
