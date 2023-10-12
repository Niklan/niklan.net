<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Environment;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\external_content\Builder\HtmlElementBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerResultInterface;
use Drupal\external_content\Contract\Configuration\ConfigurationAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Data\BundlerResult;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentBundleDocument;
use Drupal\external_content\Data\ExternalContentFileCollection;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingContainerException;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Serializer\PlainTextSerializer;
use Drupal\external_content_test\Event\BarEvent;
use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCaseTest;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Provides a test for environment.
 *
 * @group external_content
 * @coversDefaultClass \Drupal\external_content\Environment\Environment
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
  public function testHtmlParsers(): void {
    $html_parser = new class implements HtmlParserInterface {

      /**
       * {@inheritdoc}
       */
      public function parse(\DOMNode $node): HtmlParserResult {
        return HtmlParserResult::stop();
      }

    };

    $environment = new Environment();
    $environment->addHtmlParser(new $html_parser());

    $expected = [
      0 => $html_parser,
    ];

    self::assertEquals(
      $expected,
      $environment->getHtmlParsers()->getIterator()->getArrayCopy(),
    );
  }

  /**
   * {@selfdoc}
   */
  public function testBundlers(): void {
    $bundler = new class implements BundlerInterface {

      /**
       * {@inheritdoc}
       */
      public function bundle(ExternalContentDocument $document): BundlerResultInterface {
        return BundlerResult::unidentified();
      }

    };

    $environment = new Environment();
    $environment->addBundler(new $bundler());

    $expected = [
      0 => $bundler,
    ];

    self::assertEquals(
      $expected,
      $environment->getBundlers()->getIterator()->getArrayCopy(),
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
      public function find(): ExternalContentFileCollection {
        return new ExternalContentFileCollection();
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
    $builder = new class implements BuilderInterface {

      /**
       * {@inheritdoc}
       */
      public function build(NodeInterface $node, array $children): BuilderResultInterface {
        return BuilderResult::none();
      }

    };

    $environment = new Environment();
    $environment->addBuilder(new $builder());

    $expected = [
      0 => $builder,
    ];

    self::assertEquals(
      $expected,
      $environment->getBuilders()->getIterator()->getArrayCopy(),
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
      public function load(ExternalContentBundleDocument $bundle): LoaderResultInterface {
        return LoaderResult::skip();
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
      Configuration::class,
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
    $builder = new HtmlElementBuilder();

    $extension = new class ($builder) implements ExtensionInterface {

      /**
       * Constructs a new instance.
       */
      public function __construct(
        private readonly BuilderInterface $builder,
      ) {}

      /**
       * {@inheritdoc}
       */
      public function register(EnvironmentBuilderInterface $environment): void {
        $environment->addBuilder($this->builder);
      }

    };

    $environment = new Environment();
    $environment->addExtension(new $extension($builder));

    self::assertEquals(
      $builder,
      $environment->getBuilders()->getIterator()->offsetGet(0),
    );
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
    yield 'HTML Parser' => [
      'component_interface' => HtmlParserInterface::class,
      'method' => 'addHtmlParser',
    ];

    yield 'Bundler' => [
      'component_interface' => BundlerInterface::class,
      'method' => 'addBundler',
    ];

    yield 'Builder' => [
      'component_interface' => BuilderInterface::class,
      'method' => 'addBuilder',
    ];

    yield 'Finder' => [
      'component_interface' => FinderInterface::class,
      'method' => 'addFinder',
    ];

    yield 'Serializer' => [
      'component_interface' => NodeSerializerInterface::class,
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
      ->prophesize(HtmlParserInterface::class)
      ->willImplement(ContainerAwareInterface::class);

    $environment = new Environment();

    self::expectException(MissingContainerException::class);

    $environment->addHtmlParser($object->reveal());
  }

}
