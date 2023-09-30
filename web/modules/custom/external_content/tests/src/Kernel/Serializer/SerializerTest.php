<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Serializer;

use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingDeserializerException;
use Drupal\external_content\Exception\MissingSerializerException;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
use Drupal\external_content\Serializer\HtmlElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;
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
    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);
    $p = new HtmlElement('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new HtmlElement('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));
    $document->addChild($p);

    $serializer = $this->prepareSerializer();

    $json = $serializer->serialize($document);
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
      ->addSerializer(new HtmlElementSerializer())
      ->addSerializer(new ExternalContentDocumentSerializer());
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($environment);

    return $serializer;
  }

  /**
   * {@selfdoc}
   *
   * @covers \Drupal\external_content\Exception\MissingSerializerException
   */
  public function testMissingSerializerException(): void {
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment(new Environment());

    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);

    self::expectException(MissingSerializerException::class);

    $serializer->serialize($document);
  }

  /**
   * {@selfdoc}
   *
   * @covers \Drupal\external_content\Exception\MissingDeserializerException
   */
  public function testMissingDeserializerException(): void {
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment(new Environment());

    self::expectException(MissingDeserializerException::class);

    $serializer->deserialize('{"type":"external_content:document","version":"1.0.0","data":{"file":{"working_dir":"foo","pathname":"bar","data":[]}}}');
  }

}
