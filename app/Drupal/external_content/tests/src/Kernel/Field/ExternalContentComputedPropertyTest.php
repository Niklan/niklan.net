<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Field\ExternalContentComputedProperty;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @covers \Drupal\external_content\Field\ExternalContentComputedProperty
 * @group external_content
 */
final class ExternalContentComputedPropertyTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  public function testInvalidField(): void {
    $property = $this->preparePropertyInstance(violation_count: 1);

    self::assertNull($property->getValue());
  }

  public function testValidField(): void {
    $property = $this->preparePropertyInstance(violation_count: 0);

    $value = $property->getValue();
    self::assertEquals($this->prepareDocument(), $value);
    // Make sure consecutive call returns previous result.
    self::assertSame($value, $property->getValue());
  }

  private function preparePropertyInstance(int $violation_count): ExternalContentComputedProperty {
    $data_definition = DataDefinition::createFromDataType('any');

    return new ExternalContentComputedProperty(
      definition: $data_definition,
      name: 'property',
      parent: $this->prepareFieldItem($violation_count),
    );
  }

  private function prepareFieldItem(int $violation_count): FieldItemInterface {
    $violation_list = $this->prophesize(ConstraintViolationListInterface::class);
    $violation_list->count()->willReturn($violation_count);
    $violation_list = $violation_list->reveal();

    $environment_id = $this->prophesize(TypedDataInterface::class);
    $environment_id
      ->getString()
      ->willReturn('test');
    $environment_id = $environment_id->reveal();

    $value = $this->prophesize(TypedDataInterface::class);
    $value->getValue()->willReturn($this->prepareFieldItemValue());
    $value = $value->reveal();

    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->validate()->willReturn($violation_list);
    $field_item
      ->get('environment_id')
      ->willReturn($environment_id);
    $field_item->get('value')->willReturn($value);

    return $field_item->reveal();
  }

  private function prepareFieldItemValue(): string {
    $serializer_manager = $this
      ->container
      ->get(SerializerManagerInterface::class);
    $environment_manager = $this
      ->container
      ->get(EnvironmentManagerInterface::class);

    return $serializer_manager->normalize(
      node: $this->prepareDocument(),
      environment: $environment_manager->get('test'),
    );
  }

  private function prepareDocument(): Content {
    $document = new Content();
    $p = new Element('p');
    $p->addChild(new PlainText('Hello, World!'));
    $document->addChild($p);

    return $document;
  }

}
