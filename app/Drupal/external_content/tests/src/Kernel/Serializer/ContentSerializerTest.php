<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
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
final class ContentSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment('test');
    $environment->addSerializer(new ContentSerializer());

    $serializer = $this->container->get(SerializerManagerInterface::class);

    $document = new Content();
    $expected_json = <<<'JSON'
    {"type":"external_content:content","version":"1.0.0","data":{"source":[],"children":[]}}
    JSON;

    self::assertEquals(
      expected: $expected_json,
      actual: $serializer->normalize($document, $environment),
    );

    $deserialized_document = $serializer->deserialize(
      json: $expected_json,
      environment: $environment,
    );

    self::assertEquals($document, $deserialized_document);
  }

}
