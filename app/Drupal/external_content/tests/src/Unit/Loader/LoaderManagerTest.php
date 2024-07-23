<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Loader;

use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Drupal\external_content\Loader\LoaderManager;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @covers \Drupal\external_content\Loader\LoaderManager
 * @group external_content
 */
final class LoaderManagerTest extends UnitTestCase {

  public function testLoad(): void {
    $loader_skip = $this->prophesize(LoaderInterface::class);
    $loader_skip
      ->load(Argument::cetera())
      ->willReturn(LoaderResult::pass('test'));
    $loader_skip = $loader_skip->reveal();

    $result = ['test'];
    $loader_with_result = $this->prophesize(LoaderInterface::class);
    $loader_with_result
      ->load(Argument::cetera())
      ->willReturn(LoaderResult::withResults('test', $result));
    $loader_with_result = $loader_with_result->reveal();

    $bundle = new IdentifiedSourceBundle('test');

    $environment = new Environment('test');
    $environment->addLoader($loader_skip, 100);
    $environment->addLoader($loader_with_result);

    $container = $this->prophesize(ContainerInterface::class);
    $container = $container->reveal();

    $manager = new LoaderManager(
      container: $container,
      loaders: [],
    );
    $result_collection = $manager->load($bundle, $environment);
    self::assertEquals(2, $result_collection->count());

    $first_result = $result_collection->getIterator()->offsetGet(0);
    self::assertInstanceOf(LoaderResult::class, $first_result);
    self::assertSame('test', $first_result->bundleId());
    self::assertFalse($first_result->hasResults());
    self::assertTrue($first_result->hasNoResults());
    self::assertTrue($first_result->shouldContinue());
    self::assertFalse($first_result->shouldNotContinue());
    self::assertEmpty($first_result->results());

    $second_result = $result_collection->getIterator()->offsetGet(1);
    self::assertInstanceOf(LoaderResult::class, $second_result);
    self::assertSame('test', $second_result->bundleId());
    self::assertTrue($second_result->hasResults());
    self::assertFalse($second_result->hasNoResults());
    self::assertFalse($second_result->shouldContinue());
    self::assertTrue($second_result->shouldNotContinue());
    self::assertEquals($result, $second_result->results());
  }

  public function testCollection(): void {
    $loaders = ['test' => ['service' => 'loader.service']];

    $loader = $this->prophesize(LoaderInterface::class);
    $loader = $loader->reveal();

    $container = $this->prophesize(ContainerInterface::class);
    $container->get(Argument::exact('loader.service'))->willReturn($loader);
    $container = $container->reveal();

    $manager = new LoaderManager(container: $container, loaders: $loaders);
    self::assertEquals($loaders, $manager->list());
    self::assertTrue($manager->has('test'));
    self::assertSame($loader, $manager->get('test'));

    self::assertFalse($manager->has('random_string'));
    $exception = new MissingContainerDefinitionException(
      type: 'loader',
      id: 'random_string',
    );
    self::expectExceptionObject($exception);
    $manager->get('random_string');
  }

}
