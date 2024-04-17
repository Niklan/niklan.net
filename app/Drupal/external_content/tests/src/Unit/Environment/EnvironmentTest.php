<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Environment;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\external_content\Builder\ElementRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\IdentifierSource;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingContainerException;
use Drupal\external_content\Serializer\PlainTextSerializer;
use Drupal\external_content_test\Builder\EmptyRenderArrayRenderArrayBuilder;
use Drupal\external_content_test\Event\BarEvent;
use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCaseTest;
use League\Config\Configuration;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

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
  public function testEvents(): void {
    $event = new FooEvent();
    $environment = new Environment();

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
   */
  public function testIdentifiers(): void {
    $identifier = $this->prophesize(IdentifierInterface::class);
    $identifier = $identifier->reveal();

    $environment = new Environment();
    $environment->addIdentifier($identifier);

    $expected = [
      0 => $identifier,
    ];

    self::assertEquals(
      $expected,
      $environment->getIdentifiers()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testParsers(): void {
    $parser = $this->prophesize(ParserInterface::class);
    $parser = $parser->reveal();

    $environment = new Environment();
    $environment->addHtmlParser($parser);

    $expected = [
      0 => $parser,
    ];

    self::assertEquals(
      $expected,
      $environment->getHtmlParsers()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testFinders(): void {
    $finder = new class implements FinderInterface {

      /**
       * {@inheritdoc}
       */
      public function find(): SourceCollection {
        return new SourceCollection();
      }

    };

    $environment = new Environment();
    $environment->addFinder(new $finder());

    $expected = [
      0 => $finder,
    ];

    self::assertEquals(
      $expected,
      $environment->getFinders()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testBuilder(): void {
    $builder = new EmptyRenderArrayRenderArrayBuilder();

    $environment = new Environment();
    $environment->addRenderArrayBuilder($builder);

    $expected = [
      0 => $builder,
    ];

    self::assertEquals(
      $expected,
      $environment->getRenderArrayBuilders()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testLoader(): void {
    $loader = new class implements LoaderInterface {

      /**
       * {@inheritdoc}
       */
      public function load(IdentifierSource $bundle): LoaderResultInterface {
        return LoaderResult::pass();
      }

    };

    $environment = new Environment();
    $environment->addLoader(new $loader());

    $expected = [
      0 => $loader,
    ];

    self::assertEquals(
      $expected,
      $environment->getLoaders()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testConfiguration(): void {
    $environment = new Environment();

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

    $environment = new Environment();
    $environment->setEventDispatcher($event_dispatcher);
    $environment->dispatch($event);
  }

  /**
   * {@selfdoc}
   */
  public function testSerializer(): void {
    $serializer = new PlainTextSerializer();

    $environment = new Environment();
    $environment->addSerializer($serializer);

    $expected = [
      0 => $serializer,
    ];

    self::assertEquals(
      $expected,
      $environment->getSerializers()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testExtension(): void {
    $builder = new ElementRenderArrayBuilder();

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

    $environment = new Environment();
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

    $environment = new Environment();
    $environment->addExtension($extension);
  }

  /**
   * {@selfdoc}
   *
   * @dataProvider injectDependenciesDataProvider
   */
  public function testInjectDependencies(string $component_interface, string $method): void {
    $container = $this->prophesize(ContainerInterface::class);

    $object = $this
      ->prophesize($component_interface)
      ->willImplement(EnvironmentAwareInterface::class)
      ->willImplement(ConfigurationAwareInterface::class)
      ->willImplement(ContainerAwareInterface::class);

    $object
      ->setEnvironment(Argument::type(EnvironmentInterface::class))
      ->shouldBeCalled();

    $object
      ->setConfiguration(Argument::type(Configuration::class))
      ->shouldBeCalled();

    $object
      ->setContainer(Argument::type(ContainerInterface::class))
      ->shouldBeCalled();

    $environment = new Environment();
    $environment->setContainer($container->reveal());
    \call_user_func([$environment, $method], $object->reveal());
  }

  /**
   * {@selfdoc}
   */
  public function injectDependenciesDataProvider(): \Generator {
    yield 'Parser' => [
      'component_interface' => ParserInterface::class,
      'method' => 'addParser',
    ];

    yield 'Bundler' => [
      'component_interface' => BundlerInterface::class,
      'method' => 'addBundler',
    ];

    yield 'RenderArrayBuilder' => [
      'component_interface' => RenderArrayBuilderInterface::class,
      'method' => 'addBuilder',
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
  }

  /**
   * {@selfdoc}
   */
  public function testMissingContainerException(): void {
    $object = $this
      ->prophesize(ParserInterface::class)
      ->willImplement(ContainerAwareInterface::class);

    $environment = new Environment();

    self::expectException(MissingContainerException::class);

    $environment->addHtmlParser($object->reveal());
  }

}
