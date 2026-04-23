<?php

declare(strict_types=1);

namespace Drupal\Tests\app_main\Unit\Hook\Core;

use Drupal\app_main\Hook\Core\YandexMetrikaPageBottom;
use Drupal\Core\Site\Settings;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(YandexMetrikaPageBottom::class)]
final class YandexMetrikaPageBottomTest extends UnitTestCase {

  protected function setUp(): void {
    parent::setUp();
    new Settings([]);
  }

  public function testNoIdAddsNothing(): void {
    $hook = new YandexMetrikaPageBottom();
    $page_bottom = [];

    $hook($page_bottom);

    self::assertEmpty($page_bottom);
  }

  public function testEmptyStringAddsNothing(): void {
    new Settings(['app_yandex_metrika_id' => '']);
    $hook = new YandexMetrikaPageBottom();
    $page_bottom = [];

    $hook($page_bottom);

    self::assertEmpty($page_bottom);
  }

  public function testNonStringAddsNothing(): void {
    new Settings(['app_yandex_metrika_id' => 123]);
    $hook = new YandexMetrikaPageBottom();
    $page_bottom = [];

    $hook($page_bottom);

    self::assertEmpty($page_bottom);
  }

  public function testValidIdAddsImage(): void {
    new Settings(['app_yandex_metrika_id' => '48390929']);
    $hook = new YandexMetrikaPageBottom();
    $page_bottom = [];

    $hook($page_bottom);

    self::assertArrayHasKey('app_main_yandex_metrika', $page_bottom);
    self::assertSame('html_tag', $page_bottom['app_main_yandex_metrika']['#type']);
    self::assertSame('img', $page_bottom['app_main_yandex_metrika']['#tag']);
    self::assertSame('https://mc.yandex.ru/watch/48390929', $page_bottom['app_main_yandex_metrika']['#attributes']['src']);
    self::assertSame('position:absolute; left:-9999px;', $page_bottom['app_main_yandex_metrika']['#attributes']['style']);
    self::assertSame('', $page_bottom['app_main_yandex_metrika']['#attributes']['alt']);
  }

  public function testIdIsEncoded(): void {
    new Settings(['app_yandex_metrika_id' => '48<39>09']);
    $hook = new YandexMetrikaPageBottom();
    $page_bottom = [];

    $hook($page_bottom);

    self::assertSame('https://mc.yandex.ru/watch/48%3C39%3E09', $page_bottom['app_main_yandex_metrika']['#attributes']['src']);
  }

}
