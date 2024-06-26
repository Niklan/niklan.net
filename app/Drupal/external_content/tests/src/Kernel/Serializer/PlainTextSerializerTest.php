<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Serializer\PlainTextSerializer;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Serializer\PlainTextSerializer
 * @group external_content
 */
final class PlainTextSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment('test');
    $environment->addSerializer(new PlainTextSerializer());

    $serializer = $this->container->get(SerializerManagerInterface::class);

    $element = new PlainText('Hello, World!');
    $expected_json = <<<'JSON'
    {"type":"external_content:plain_text","version":"1.0.0","data":{"text":"Hello, World!"}}
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
