<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Html\PlainText;
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
    $environment = new Environment();
    $environment->addSerializer(new PlainTextSerializer());

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    $element = new PlainText('Hello, World!');
    $expected_json = '{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"Hello, World!"},"children":[]}';

    self::assertEquals($expected_json, $serializer->normalize($element));

    $deserialized_element = $serializer->deserialize($expected_json);

    self::assertEquals($element, $deserialized_element);
  }

}
