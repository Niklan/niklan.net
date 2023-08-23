<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Environment;

use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Event\FooEvent;
use Drupal\Tests\UnitTestCaseTest;

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
    $configuration = new Configuration();
    $environment = new Environment($configuration);

    self::assertFalse($environment->getListenersForEvent($event)->valid());

    $listener_called = FALSE;
    $listener = static function () use (&$listener_called): void {
      $listener_called = TRUE;
    };

    $environment->addEventListener(FooEvent::class, $listener);
    self::assertTrue($environment->getListenersForEvent($event)->valid());
    self::assertFalse($listener_called);

    $environment->dispatch($event);
    self::assertTrue($listener_called);
  }

  /**
   * {@selfdoc}
   */
  public function testHtmlParsers(): void {
    $html_parser = new class implements HtmlParserInterface{

      /**
       * {@inheritdoc}
       */
      public function parse(\DOMNode $node): HtmlParserResult {
        return HtmlParserResult::stop();
      }

    };
    $environment = new Environment(new Configuration());
    $environment->addHtmlParser($html_parser::class);

    $expected = [
      0 => $html_parser::class,
    ];

    self::assertEquals($expected, $environment->getHtmlParsers()->getIterator()->getArrayCopy());
  }

}
