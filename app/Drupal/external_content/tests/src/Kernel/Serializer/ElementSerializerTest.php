<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Serializer\ElementSerializer;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Serializer\ElementSerializer
 * @group external_content
 */
final class ElementSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment('test');
    $environment->addSerializer(new ElementSerializer());

    $serializer = $this->container->get(SerializerManagerInterface::class);

    $attributes = new Attributes();
    $attributes->setAttribute('foo', 'bar');
    $element = new Element('div', $attributes);
    $expected_json = <<<'JSON'
    {"type":"external_content:html_element","version":"1.0.0","data":{"tag":"div","attributes":{"foo":"bar"},"children":[]}}
    JSON;

    self::assertEquals(
      expected: $expected_json,
      actual: $serializer->normalize($element, $environment),
    );

    $deserialized_element = $serializer->deserialize(
      json: $expected_json,
      environment: $environment,
    );

    self::assertEquals($element, $deserialized_element);
  }

}
