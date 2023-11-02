<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Serializer\ExternalContentDocumentSerializer
 * @group external_content
 */
final class ExternalContentDocumentSerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $environment = new Environment();
    $environment->addSerializer(new ExternalContentDocumentSerializer());

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    $file = new File('foo', 'bar', 'html');
    $document = new Content($file);
    $expected_json = '{"type":"external_content:document","version":"1.0.0","data":{"file":{"working_dir":"foo","pathname":"bar","data":[]}},"children":[]}';

    self::assertEquals($expected_json, $serializer->normalize($document));

    $deserialized_document = $serializer->deserialize($expected_json);

    self::assertEquals($document, $deserialized_document);
  }

}
