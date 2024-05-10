<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Environment;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Event\BarEvent;
use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCaseTest;
use League\Config\Configuration;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Provides a test for environment.
 *
 * @group external_content
 * @covers \Drupal\external_content\Environment\Environment
 */
final class EnvironmentTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testId(): void {
    $id = $this->randomString();
    $environment = new Environment($id);
    self::assertSame($id, $environment->id());
  }

  /**
   * {@selfdoc}
   */
  public function testEvents(): void {
    $event = new FooEvent();
    $environment = new Environment('test');

    self::assertFalse($environment->getListenersForEvent($event)->valid());

    $bar_listener_called = FALSE;
    $bar_listener = static function () use (&$bar_listener_called): void {
      $bar_listener_called = TRUE;
    };
    $environment->addEventListener(BarEvent::class, $bar_listener);

    $listener_called = FALSE;
    $listener = static function () use (&$listener_called): void {
      $listener_called = TRUE;
    };

    $stop_listener = static function (FooEvent $event): void {
      $event->stopPropagation();
    };

    $unreachable_listener_called = FALSE;
    $unreachable_listener = static function () use (&$unreachable_listener_called): void {
      $unreachable_listener_called = TRUE;
    };

    $environment->addEventListener(FooEvent::class, $listener);
    $environment->addEventListener(FooEvent::class, $stop_listener);
    $environment->addEventListener(FooEvent::class, $unreachable_listener);

    self::assertTrue($environment->getListenersForEvent($event)->valid());
    self::assertFalse($listener_called);

    $environment->dispatch($event);

    self::assertTrue($listener_called);
    self::assertFalse($unreachable_listener_called);
    // It shouldn't be called because it subscribed to a different event.
    self::assertFalse($bar_listener_called);
  }

  /**
   * {@selfdoc}
   *
   * @dataProvider collectionsDataProvider
   */
  public function testCollections(string $implements, string $setter, string $getter): void {
    $instance = $this->prophesize($implements);
    $instance = $instance->reveal();

    $environment = new Environment('test');
    \call_user_func([$environment, $setter], $instance);

    $result = \call_user_func([$environment, $getter])
      ->getIterator()
      ->getArrayCopy();

    self::assertSame([0 => $instance], $result);
  }

  /**
   * {@selfdoc}
   */
  public function collectionsDataProvider(): \Generator {
    yield 'identifiers' => [
      'implements' => IdentifierInterface::class,
      'setter' => 'addIdentifier',
      'getter' => 'getIdentifiers',
    ];

    yield 'HTML parsers' => [
      'implements' => HtmlParserInterface::class,
      'setter' => 'addHtmlParser',
      'getter' => 'getHtmlParsers',
    ];

    yield 'finders' => [
      'implements' => FinderInterface::class,
      'setter' => 'addFinder',
      'getter' => 'getFinders',
    ];

    yield 'render array builders' => [
      'implements' => RenderArrayBuilderInterface::class,
      'setter' => 'addRenderArrayBuilder',
      'getter' => 'getRenderArrayBuilders',
    ];

    yield 'loaders' => [
      'implements' => LoaderInterface::class,
      'setter' => 'addLoader',
      'getter' => 'getLoaders',
    ];

    yield 'serializers' => [
      'implements' => SerializerInterface::class,
      'setter' => 'addSerializer',
      'getter' => 'getSerializers',
    ];

    yield 'bundlers' => [
      'implements' => BundlerInterface::class,
      'setter' => 'addBundler',
      'getter' => 'getBundlers',
    ];

    yield 'converters' => [
      'implements' => ConverterInterface::class,
      'setter' => 'addConverter',
      'getter' => 'getConverters',
    ];
  }

  /**
   * {@selfdoc}
   */
  public function testConfiguration(): void {
    $environment = new Environment('test');

    self::assertInstanceOf(
      ConfigurationInterface::class,
      $environment->getConfiguration(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testCustomEventDispatcher(): void {
    $event_dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $event_dispatcher
      ->dispatch(Argument::cetera())
      ->shouldBeCalledOnce()
      ->willReturn(new \stdClass());
    $event_dispatcher = $event_dispatcher->reveal();

    $event = $this->prophesize(StoppableEventInterface::class);
    $event = $event->reveal();

    $environment = new Environment('test');
    $environment->setEventDispatcher($event_dispatcher);
    $environment->dispatch($event);
  }

  /**
   * {@selfdoc}
   */
  public function testExtension(): void {
    $builder = $this->prophesize(RenderArrayBuilderInterface::class);
    $builder = $builder->reveal();

    $extension = new class ($builder) implements ExtensionInterface {

      /**
       * Constructs a new instance.
       */
      public function __construct(
        private readonly RenderArrayBuilderInterface $builder,
      ) {}

      /**
       * {@inheritdoc}
       */
      public function register(EnvironmentBuilderInterface $environment): void {
        $environment->addRenderArrayBuilder($this->builder);
      }

    };

    $environment = new Environment('test');
    $environment->addExtension(new $extension($builder));

    self::assertEquals(
      $builder,
      $environment->getRenderArrayBuilders()->getIterator()->offsetGet(0),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testExtensionConfigureSchema(): void {
    $extension = $this->prophesize(ConfigurableExtensionInterface::class);
    $extension->register(Argument::cetera())->shouldBeCalled();
    $extension->configureSchema(Argument::cetera())->shouldBeCalled();
    $extension = $extension->reveal();

    $environment = new Environment('test');
    $environment->addExtension($extension);
  }

  /**
   * {@selfdoc}
   *
   * @dataProvider injectDependenciesDataProvider
   */
  public function testInjectDependencies(string $component_interface, string $method): void {
    $object = $this
      ->prophesize($component_interface)
      ->willImplement(EnvironmentAwareInterface::class)
      ->willImplement(ConfigurationAwareInterface::class);

    $object
      ->setEnvironment(Argument::type(EnvironmentInterface::class))
      ->shouldBeCalled();

    $object
      ->setConfiguration(Argument::type(Configuration::class))
      ->shouldBeCalled();

    $environment = new Environment('test', []);
    \call_user_func([$environment, $method], $object->reveal());
  }

  /**
   * {@selfdoc}
   */
  public function injectDependenciesDataProvider(): \Generator {
    yield 'HTML Parser' => [
      'component_interface' => HtmlParserInterface::class,
      'method' => 'addHtmlParser',
    ];

    yield 'Bundler' => [
      'component_interface' => BundlerInterface::class,
      'method' => 'addBundler',
    ];

    yield 'RenderArrayBuilder' => [
      'component_interface' => RenderArrayBuilderInterface::class,
      'method' => 'addRenderArrayBuilder',
    ];

    yield 'FinderFacade' => [
      'component_interface' => FinderInterface::class,
      'method' => 'addFinder',
    ];

    yield 'SerializerManager' => [
      'component_interface' => SerializerInterface::class,
      'method' => 'addSerializer',
    ];

    yield 'Loader' => [
      'component_interface' => LoaderInterface::class,
      'method' => 'addLoader',
    ];

    yield 'Converter' => [
      'component_interface' => ConverterInterface::class,
      'method' => 'addConverter',
    ];
  }

}
