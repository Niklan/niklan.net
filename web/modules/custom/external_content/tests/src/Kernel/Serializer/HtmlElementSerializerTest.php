<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Serializer\HtmlElementSerializer;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Serializer\HtmlElementSerializer
 * @group external_content
 */
final class HtmlElementSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment();
    $environment->addSerializer(HtmlElementSerializer::class);

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    $attributes = new Attributes();
    $attributes->setAttribute('foo', 'bar');
    $element = new HtmlElement('div', $attributes);
    $expected_json = '{"type":"external_content:html_element","data":{"tag":"div","attributes":{"foo":"bar"}},"children":[]}';

    self::assertEquals($expected_json, $serializer->serialize($element));

    $deserialized_element = $serializer->deserialize($expected_json);

    self::assertEquals($element, $deserialized_element);
  }

}
