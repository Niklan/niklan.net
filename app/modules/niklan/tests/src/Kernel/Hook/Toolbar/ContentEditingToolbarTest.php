<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Hook\Toolbar;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\niklan\Hook\Toolbar\ContentEditingToolbar;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ContentEditingToolbar::class)]
final class ContentEditingToolbarTest extends NiklanTestBase {

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

    self::assertArrayHasKey('niklan_content_editing', $result);
    // With unsupported route it should only contain cache metadata.
    self::assertCount(1, $result['niklan_content_editing']);
    self::assertArrayHasKey('#cache', $result['niklan_content_editing']);
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

    self::assertArrayHasKey('niklan_content_editing', $result);
    self::assertArrayHasKey('tab', $result['niklan_content_editing']);
    self::assertArrayHasKey('tray', $result['niklan_content_editing']);
    self::assertArrayHasKey('#cache', $result['niklan_content_editing']);
  }

}
