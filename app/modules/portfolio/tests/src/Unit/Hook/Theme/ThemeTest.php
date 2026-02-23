<?php

declare(strict_types=1);

namespace Drupal\Tests\app_portfolio\Unit\Hook\Theme;

use Drupal\app_portfolio\Hook\Theme\Theme;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Theme::class)]
final class ThemeTest extends UnitTestCase {

  public function testPortfolioListThemeHook(): void {
    $theme = new Theme();
    $result = $theme();

    self::assertArrayHasKey('app_portfolio_list', $result);
    self::assertArrayHasKey('variables', $result['app_portfolio_list']);
    self::assertArrayHasKey('description', $result['app_portfolio_list']['variables']);
    self::assertArrayHasKey('items', $result['app_portfolio_list']['variables']);
  }

  public function testThemeHookCount(): void {
    $theme = new Theme();
    $result = $theme();

    self::assertCount(1, $result);
  }

}
