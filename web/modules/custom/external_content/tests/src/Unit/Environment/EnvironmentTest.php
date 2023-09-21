<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Environment;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerResultInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\BundlerResult;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentFileCollection;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content_test\Event\BarEvent;
use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCaseTest;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

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
    $environment->addHtmlParser($html_parser::class);

    $expected = [
      0 => $html_parser::class,
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
    $environment->addBundler($bundler::class);

    $expected = [
      0 => $bundler::class,
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
    $environment->addFinder($finder::class);

    $expected = [
      0 => $finder::class,
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
    $builder = new class implements BundlerInterface {

      /**
       * {@inheritdoc}
       */
      public function bundle(ExternalContentDocument $document): BundlerResultInterface {
        return BundlerResult::unidentified();
      }

    };

    $environment = new Environment();
    $environment->addBuilder($builder::class);

    $expected = [
      0 => $builder::class,
    ];

    self::assertEquals(
      $expected,
      $environment->getBuilders()->getIterator()->getArrayCopy(),
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

}
