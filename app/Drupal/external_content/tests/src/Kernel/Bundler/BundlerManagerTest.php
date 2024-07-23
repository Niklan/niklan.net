<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Bundler;

use Drupal\external_content\Bundler\SameIdBundler;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Source\FooSource;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content bundler.
 *
 * @covers \Drupal\external_content\Bundler\BundlerManager
 * @group external_content
 */
final class BundlerManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected BundlerManagerInterface $bundler;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  public function testBundler(): void {
    $environment = new Environment('test');
    $environment->addBundler(new SameIdBundler());

    $item_a = new IdentifiedSource(
      id: 'foo',
      source: new FooSource(
        type: 'test',
        contents: 'test',
      ),
    );
    $item_b = new IdentifiedSource(
      id: 'foo',
      source: new FooSource(
        type: 'test',
        contents: 'test',
      ),
    );
    $item_c = new IdentifiedSource(
      id: 'bar',
      source: new FooSource(
        type: 'test',
        contents: 'test',
      ),
    );

    $collection = new IdentifiedSourceCollection();
    $collection->add($item_a);
    $collection->add($item_b);
    $collection->add($item_c);

    $result = $this->bundler->bundle($collection, $environment);

    $bundles = $result->bundles();
    self::assertCount(2, $bundles);
    self::assertSame('foo', $bundles[0]->id);
    self::assertCount(2, $bundles[0]->sources());
    self::assertSame('bar', $bundles[1]->id);
    self::assertCount(1, $bundles[1]->sources());
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->bundler = $this
      ->container
      ->get(BundlerManagerInterface::class);
  }

}
