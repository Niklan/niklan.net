<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\Html;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a test for external content HTML parser.
 *
 * @group external_content
 * @covers \Drupal\external_content\Parser\HtmlParserManager
 */
final class HtmlParserManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * {@selfdoc}
   */
  public function testParse(): void {
    $event_dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $event_dispatcher
      ->dispatch(Argument::any())
      ->willReturnArgument()
      ->shouldBeCalledOnce();
    $event_dispatcher = $event_dispatcher->reveal();

    $environment = $this->getEnvironmentManager()->get('test');
    $environment->setEventDispatcher($event_dispatcher);

    $html = new Html('<p>Hello, <strong>World</strong>!</p>');
    $result = $this->getHtmlParserManager()->parse($html, $environment);

    self::assertInstanceOf(Content::class, $result);
    self::assertTrue($result->hasChildren());
    self::assertCount(1, $result->getChildren());
  }

  /**
   * {@selfdoc}
   */
  private function getHtmlParserManager(): HtmlParserManagerInterface {
    return $this->container->get(HtmlParserManagerInterface::class);
  }

  /**
   * {@selfdoc}
   */
  private function getEnvironmentManager(): EnvironmentManagerInterface {
    return $this->container->get(EnvironmentManagerInterface::class);
  }

}
