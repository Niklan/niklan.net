<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content serializer.
 *
 * @covers \Drupal\external_content\Serializer\SerializerManager
 * @group external_content
 */
final class SerializerManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  public function testSerialization(): void {
    $environment = $this->getEnvironmentManager()->get('test');

    $document = new Content();
    $p = new Element('p');
    $p->addChild(new PlainText('Hello, '));
    $p->addChild((new Element('strong'))->addChild(new PlainText('World')));
    $p->addChild(new PlainText('!'));
    $document->addChild($p);

    $json = $this->getSerializerManager()->normalize($document, $environment);
    $expected_json = <<<'JSON'
    {"type":"external_content:content","version":"1.0.0","data":{"source":[],"children":[{"type":"external_content:html_element","version":"1.0.0","data":{"tag":"p","children":[{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"Hello, "}},{"type":"external_content:html_element","version":"1.0.0","data":{"tag":"strong","children":[{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"World"}}]}},{"type":"external_content:plain_text","version":"1.0.0","data":{"text":"!"}}]}}]}}
    JSON;

    self::assertEquals($expected_json, $json);

    $document_from_json = $this->getSerializerManager()->deserialize(
      json: $expected_json,
      environment: $environment,
    );

    self::assertEquals($document, $document_from_json);
  }

  private function getSerializerManager(): SerializerManagerInterface {
    return $this->container->get(SerializerManagerInterface::class);
  }

  private function getEnvironmentManager(): EnvironmentManagerInterface {
    return $this->container->get(EnvironmentManagerInterface::class);
  }

}
