<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Field;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\external_content\Field\ExternalContentComputedProperty;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Field\ExternalContentComputedProperty
 * @group external_content *
 */
final class ExternalContentComputedPropertyTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * {@selfdoc}
   */
  public function testInvalidField(): void {
    $property = $this->preparePropertyInstance([
      'violation_count' => 1,
    ]);

    self::assertNull($property->getValue());
  }

  /**
   * {@selfdoc}
   */
  private function preparePropertyInstance(array $field_data): ExternalContentComputedProperty {
    $data_definition = DataDefinition::createFromDataType('any');

    return new ExternalContentComputedProperty(
      definition: $data_definition,
      name: 'property',
      parent: $this->prepareFieldItem($field_data),
    );
  }

  /**
   * {@selfdoc}
   */
  private function prepareFieldItem(array $field_data): ExternalContentFieldItem {
    $field_data += [
      'violation_count' => 0,
    ];

    $violation_list = $this->prophesize(ConstraintViolationListInterface::class);
    $violation_list->count()->willReturn($field_data['violation_count']);
    $violation_list = $violation_list->reveal();

    // @todo Add interface and prophesize it.
    $field_item = $this->prophesize(ExternalContentFieldItem::class);
    $field_item->validate()->willReturn($violation_list);
    $field_item = $field_item->reveal();

    return $field_item;
  }

}
