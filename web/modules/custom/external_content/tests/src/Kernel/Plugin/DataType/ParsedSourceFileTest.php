<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\DataType;

use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for parsed source file data type.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\DataType\ParsedSourceFile
 */
final class ParsedSourceFileTest extends ExternalContentTestBase {

  /**
   * The typed data manager.
   */
  protected ?TypedDataManagerInterface $typedDataManager;

  /**
   * Tests that data type works as expected.
   */
  public function testDataType(): void {
    $definition = $this->typedDataManager->createDataDefinition(
      'external_content_parsed_source_file',
    );
    /** @var \Drupal\external_content\Plugin\DataType\ParsedSourceFile $typed_data */
    $typed_data = $this->typedDataManager->create($definition);
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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->typedDataManager = $this->container->get('typed_data_manager');
  }

}
