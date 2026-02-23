<?php

declare(strict_types=1);

namespace Drupal\Tests\app_main\Kernel\Hook\Toolbar;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\app_main\Hook\Toolbar\ContentEditingToolbar;
use Drupal\Tests\app_main\Kernel\AppMainTestBase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ContentEditingToolbar::class)]
final class ContentEditingToolbarTest extends AppMainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['toolbar'];

  /**
   * Tests toolbar on unsupported route.
   */
  public function testUnsupportedRoute(): void {
    $implementation = $this->container->get(ContentEditingToolbar::class);

    $result = $implementation();

    self::assertArrayHasKey('app_main_content_editing', $result);
    // With unsupported route it should only contain cache metadata.
    self::assertCount(1, $result['app_main_content_editing']);
    self::assertArrayHasKey('#cache', $result['app_main_content_editing']);
  }

  /**
   * Tests toolbar on supported route.
   */
  public function testSupportedRoute(): void {
    $route_match = $this->prophesize(RouteMatchInterface::class);
    $route_match->getRouteName()->willReturn('entity.user.canonical');
    $this->container->set('current_route_match', $route_match->reveal());

    $implementation = $this->container->get(ContentEditingToolbar::class);

    $result = $implementation();

    self::assertArrayHasKey('app_main_content_editing', $result);
    self::assertArrayHasKey('tab', $result['app_main_content_editing']);
    self::assertArrayHasKey('tray', $result['app_main_content_editing']);
    self::assertArrayHasKey('#cache', $result['app_main_content_editing']);
  }

}
