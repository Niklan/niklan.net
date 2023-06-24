<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Bundler;

use Drupal\external_content\Contract\Bundler\ExternalContentBundlerInterface;
use Drupal\external_content\Data\ExternalContentDocumentCollection;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content bundler.
 *
 * @covers \Drupal\external_content\Bundler\ExternalContentBundler
 */
final class ExternalContentBundlerTest extends ExternalContentTestBase {

  /**
   * The external content bundler.
   */
  protected ExternalContentBundlerInterface $bundler;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->bundler = $this
      ->container
      ->get(ExternalContentBundlerInterface::class);
  }

  /**
   * Tests that bundler works as expected.
   */
  public function testBundler(): void {
    $file_a = new ExternalContentFile('foo', 'bar');
    $file_a->getData()->set('id', '1');
    $file_a->getData()->set('language', 'ru');
    $document_a = new ExternalContentDocument($file_a);

    $file_b = new ExternalContentFile('bar', 'baz');
    $file_b->getData()->set('id', '1');
    $file_b->getData()->set('language', 'en');
    $document_b = new ExternalContentDocument($file_b);

    $file_c = new ExternalContentFile('baz', 'foo');
    $file_c->getData()->set('id', '2');
    $file_c->getData()->set('language', 'ru');
    $document_c = new ExternalContentDocument($file_c);

    $document_collection = new ExternalContentDocumentCollection();
    $document_collection->add($document_a);
    $document_collection->add($document_b);
    $document_collection->add($document_c);

    $bundle_collection = $this->bundler->bundle($document_collection);

    self::assertCount(2, $bundle_collection);
  }

}
