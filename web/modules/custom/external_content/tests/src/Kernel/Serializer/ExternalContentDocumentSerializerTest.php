<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Serializer\ContentSerializer;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Serializer\ContentSerializer
 * @group external_content
 */
final class ExternalContentDocumentSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment();
    $environment->addSerializer(new ContentSerializer());

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    $document = new Content();
    $expected_json = <<<'JSON'
    {"type":"external_content:document","version":"1.0.0","data":[],"children":[]}
    JSON;

    self::assertEquals($expected_json, $serializer->normalize($document));

    $deserialized_document = $serializer->deserialize($expected_json);

    self::assertEquals($document, $deserialized_document);
  }

}
