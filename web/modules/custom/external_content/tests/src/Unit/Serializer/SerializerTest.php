<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Serializer;

use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Serializer\Serializer;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for external content serializer.
 *
 * @covers \Drupal\external_content\Serializer\Serializer
 * @group external_content
 */
final class SerializerTest extends UnitTestCase {

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

    $serializer = new Serializer();

    $json = $serializer->serialize($document);
    $expected_json = <<<'JSON'
    {"type":"Drupal\\external_content\\Node\\ExternalContentDocument","data":{"file":{"working_dir":"foo","pathname":"bar","data":[]}},"children":[{"type":"Drupal\\external_content\\Node\\HtmlElement","data":{"tag":"p","attributes":[]},"children":[{"type":"Drupal\\external_content\\Node\\PlainText","data":{"text":"Hello, "},"children":[]},{"type":"Drupal\\external_content\\Node\\HtmlElement","data":{"tag":"strong","attributes":[]},"children":[{"type":"Drupal\\external_content\\Node\\PlainText","data":{"text":"World"},"children":[]}]},{"type":"Drupal\\external_content\\Node\\PlainText","data":{"text":"!"},"children":[]}]}]}
    JSON;

    self::assertEquals($expected_json, $json);

    $document_from_json = $serializer->deserialize($expected_json);

    self::assertEquals($document, $document_from_json);
  }

}
