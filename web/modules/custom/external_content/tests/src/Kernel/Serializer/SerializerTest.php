<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingDeserializerException;
use Drupal\external_content\Exception\MissingSerializerException;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Node\Html\PlainText;
use Drupal\external_content\Serializer\ContentSerializer;
use Drupal\external_content\Serializer\ElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content serializer.
 *
 * @covers \Drupal\external_content\Serializer\Serializer
 * @group external_content
 */
final class SerializerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testSerialization(): void {
    $file = new File('foo', 'bar', 'html');
    $document = new Content($file);
    $p = new Element('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new Element('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));
    $document->addChild($p);

    $serializer = $this->prepareSerializer();

    $json = $serializer->normalize($document);
    $expected_json = <<<'JSON'
    {"type":"external_content:document","version":"1.0.0","data":{"file":{"working_dir":"foo","pathname":"bar","data":[]}},"children":[{"type":"external_content:html_element","version":"1.0.0","data":{"tag":"p","attributes":[]},"children":[{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"Hello, "},"children":[]},{"type":"external_content:html_element","version":"1.0.0","data":{"tag":"strong","attributes":[]},"children":[{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"World"},"children":[]}]},{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"!"},"children":[]}]}]}
    JSON;

    self::assertEquals($expected_json, $json);

    $document_from_json = $serializer->deserialize($expected_json);

    self::assertEquals($document, $document_from_json);
  }

  /**
   * {@selfdoc}
   */
  private function prepareSerializer(): SerializerInterface {
    $environment = new Environment();
    $environment
      ->addSerializer(new PlainTextSerializer())
      ->addSerializer(new ElementSerializer())
      ->addSerializer(new ContentSerializer());
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    return $serializer;
  }

  /**
   * {@selfdoc}
   */
  public function testMissingSerializerException(): void {
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment(new Environment());

    $file = new File('foo', 'bar', 'html');
    $document = new Content($file);

    self::expectException(MissingSerializerException::class);

    $serializer->normalize($document);
  }

  /**
   * {@selfdoc}
   */
  public function testMissingDeserializerException(): void {
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment(new Environment());

    self::expectException(MissingDeserializerException::class);

    $serializer->deserialize('{"type":"external_content:document","version":"1.0.0","data":{"file":{"working_dir":"foo","pathname":"bar","data":[]}}}');
  }

}
