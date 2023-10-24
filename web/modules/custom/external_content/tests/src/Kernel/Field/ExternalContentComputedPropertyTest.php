<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Field\ExternalContentComputedProperty;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * {@selfdoc}
 *
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

  /**
   * {@selfdoc}
   */
  public function testInvalidField(): void {
    $property = $this->preparePropertyInstance(violation_count: 1);

    self::assertNull($property->getValue());
  }

  /**
   * {@selfdoc}
   */
  public function testValidField(): void {
    $property = $this->preparePropertyInstance(violation_count: 0);

    $value = $property->getValue();
    self::assertEquals($this->prepareDocument(), $value);
    // Make sure consecutive call returns previous result.
    self::assertSame($value, $property->getValue());
  }

  /**
   * {@selfdoc}
   */
  private function preparePropertyInstance(int $violation_count): ExternalContentComputedProperty {
    $data_definition = DataDefinition::createFromDataType('any');

    return new ExternalContentComputedProperty(
      definition: $data_definition,
      name: 'property',
      parent: $this->prepareFieldItem($violation_count),
    );
  }

  /**
   * {@selfdoc}
   */
  private function prepareFieldItem(int $violation_count): FieldItemInterface {
    $violation_list = $this->prophesize(ConstraintViolationListInterface::class);
    $violation_list->count()->willReturn($violation_count);
    $violation_list = $violation_list->reveal();

    $environment_plugin_id = $this->prophesize(TypedDataInterface::class);
    $environment_plugin_id
      ->getString()
      ->willReturn($this->getFieldItemEnvironmentPluginId());
    $environment_plugin_id = $environment_plugin_id->reveal();

    $value = $this->prophesize(TypedDataInterface::class);
    $value->getValue()->willReturn($this->prepareFieldItemValue());
    $value = $value->reveal();

    $field_item = $this->prophesize(FieldItemInterface::class);
    $field_item->validate()->willReturn($violation_list);
    $field_item
      ->get('environment_plugin_id')
      ->willReturn($environment_plugin_id);
    $field_item->get('value')->willReturn($value);

    return $field_item->reveal();
  }

  /**
   * {@selfdoc}
   */
  private function prepareFieldItemValue(): string {
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($this->getEnvironment());

    return $serializer->serialize($this->prepareDocument());
  }

  /**
   * {@selfdoc}
   */
  private function getEnvironment(): EnvironmentInterface {
    $environment_plugin_manager = $this
      ->container
      ->get(EnvironmentPluginManagerInterface::class);
    \assert($environment_plugin_manager instanceof EnvironmentPluginManagerInterface);

    $environment_plugin = $environment_plugin_manager
      ->createInstance('field_item');
    \assert($environment_plugin instanceof EnvironmentPluginInterface);

    return $environment_plugin->getEnvironment();
  }

  /**
   * {@selfdoc}
   */
  private function getFieldItemEnvironmentPluginId(): string {
    return 'field_item';
  }

  /**
   * {@selfdoc}
   */
  private function prepareDocument(): Content {
    $file = new File('foo', 'bar');
    $document = new Content($file);
    $p = new HtmlElement('p');
    $p->addChild(new PlainText('Hello, World!'));
    $document->addChild($p);

    return $document;
  }

}
