<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\ContentCollection;
use Drupal\external_content\Data\IdentifierSource;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Content;
use Drupal\external_content_test\Bundler\FrontMatterIdLanguageBundler;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content bundler.
 *
 * @covers \Drupal\external_content\Bundler\BundlerManager
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
  protected BundlerManagerInterface $bundler;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->bundler = $this
      ->container
      ->get(BundlerManagerInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testBundler(): void {
    $environment = new Environment();
    $environment->addIdentifier(new FrontMatterIdLanguageBundler());
    $this->bundler->setEnvironment($environment);

    $document_a = new Content();
    $document_a->getData()->set('front_matter', [
      'id' => '1',
      'language' => 'ru',
    ]);

    $document_b = new Content();
    $document_b->getData()->set('front_matter', [
      'id' => '1',
      'language' => 'en',
    ]);

    $document_c = new Content();
    $document_c->getData()->set('front_matter', [
      'id' => '2',
      'language' => 'ru',
    ]);

    // This must be treated as unidentified.
    $document_d = new Content();

    $document_collection = new ContentCollection();
    $document_collection->add($document_a);
    $document_collection->add($document_b);
    $document_collection->add($document_c);
    $document_collection->add($document_d);

    $bundle_collection = $this->bundler->bundle($document_collection);
    self::assertCount(2, $bundle_collection);

    $bundle = $bundle_collection->getIterator()->current();
    self::assertInstanceOf(ContentBundle::class, $bundle);
    self::assertEquals('1', $bundle->id);

    $source_variant = $bundle->getIterator()->current();
    self::assertInstanceOf(IdentifierSource::class, $source_variant);
    self::assertEquals(
      'ru',
      $source_variant->attributes->getAttribute('language'),
    );
  }

}
