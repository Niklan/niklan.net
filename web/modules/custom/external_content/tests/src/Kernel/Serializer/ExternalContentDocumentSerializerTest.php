<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
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
    $environment->addSerializer(ExternalContentDocumentSerializer::class);

    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);
    $expected_json = '{"type":"external_content:document","version":"1.0.0","data":{"file":{"working_dir":"foo","pathname":"bar","data":[]}},"children":[]}';

    self::assertEquals($expected_json, $serializer->serialize($document));

    $deserialized_document = $serializer->deserialize($expected_json);

    self::assertEquals($document, $deserialized_document);
  }

}
