<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerFacadeInterface;
use Drupal\external_content\Data\ExternalContentBundle;
use Drupal\external_content\Data\ExternalContentBundleDocument;
use Drupal\external_content\Data\ExternalContentDocumentCollection;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content_test\Bundler\FrontMatterIdLanguageBundler;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content bundler.
 *
 * @covers \Drupal\external_content\Bundler\BundlerFacade
 * @group external_content
 */
final class BundlerFacadeTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The external content bundler.
   */
  protected BundlerFacadeInterface $bundler;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->bundler = $this
      ->container
      ->get(BundlerFacadeInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testBundler(): void {
    $environment = new Environment();
    $environment->addBundler(new FrontMatterIdLanguageBundler());
    $this->bundler->setEnvironment($environment);

    $file_a = new ExternalContentFile('foo', 'bar');
    $file_a->getData()->set('front_matter', [
      'id' => '1',
      'language' => 'ru',
    ]);
    $document_a = new ExternalContentDocument($file_a);

    $file_b = new ExternalContentFile('bar', 'baz');
    $file_b->getData()->set('front_matter', [
      'id' => '1',
      'language' => 'en',
    ]);
    $document_b = new ExternalContentDocument($file_b);

    $file_c = new ExternalContentFile('baz', 'foo');
    $file_c->getData()->set('front_matter', [
      'id' => '2',
      'language' => 'ru',
    ]);
    $document_c = new ExternalContentDocument($file_c);

    // This must be treated as unidentified.
    $file_d = new ExternalContentFile('baz', 'foo');
    $document_d = new ExternalContentDocument($file_d);

    $document_collection = new ExternalContentDocumentCollection();
    $document_collection->add($document_a);
    $document_collection->add($document_b);
    $document_collection->add($document_c);
    $document_collection->add($document_d);

    $bundle_collection = $this->bundler->bundle($document_collection);
    self::assertCount(2, $bundle_collection);

    $bundle = $bundle_collection->getIterator()->current();
    self::assertInstanceOf(ExternalContentBundle::class, $bundle);
    self::assertEquals('1', $bundle->getId());

    $document = $bundle->getIterator()->current();
    self::assertInstanceOf(ExternalContentBundleDocument::class, $document);
    self::assertEquals(
      'ru',
      $document->getAttributes()->getAttribute('language'),
    );
  }

}
