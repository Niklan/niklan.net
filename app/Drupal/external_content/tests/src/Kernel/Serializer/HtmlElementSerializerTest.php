<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Serializer\ElementSerializer;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Serializer\ElementSerializer
 * @group external_content
 */
final class HtmlElementSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment();
    $environment->addSerializer(new ElementSerializer());

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    $attributes = new Attributes();
    $attributes->setAttribute('foo', 'bar');
    $element = new Element('div', $attributes);
    $expected_json = '{"type":"external_content:html_element","version":"1.0.0","data":{"tag":"div","attributes":{"foo":"bar"}},"children":[]}';

    self::assertEquals($expected_json, $serializer->normalize($element));

    $deserialized_element = $serializer->deserialize($expected_json);

    self::assertEquals($element, $deserialized_element);
  }

}
