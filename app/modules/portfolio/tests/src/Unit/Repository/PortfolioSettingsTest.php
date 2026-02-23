<?php

declare(strict_types=1);

namespace Drupal\Tests\app_portfolio\Unit\Repository;

use Prophecy\Argument;
use Drupal\app_contract\Contract\LanguageAwareStore\LanguageAwareFactory;
use Drupal\app_portfolio\Repository\PortfolioSettings;
use Drupal\app_contract\Contract\LanguageAwareStore\LanguageAwareStore;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PortfolioSettings::class)]
final class PortfolioSettingsTest extends UnitTestCase {

  public function testDefaultDescription(): void {
    $settings = $this->buildSettings([]);

    self::assertSame('The portfolio page description.', $settings->getDescription());
  }

  public function testGetDescription(): void {
    $settings = $this->buildSettings(['description' => 'Custom description']);

    self::assertSame('Custom description', $settings->getDescription());
  }

  public function testSetDescription(): void {
    $store = $this->prophesize(LanguageAwareStore::class);
    $store->set('description', 'New description')->shouldBeCalled();
    $store->get('description', 'The portfolio page description.')->willReturn('New description');

    $settings = $this->buildSettingsWithStore($store->reveal());
    $result = $settings->setDescription('New description');

    self::assertSame($settings, $result);
  }

  public function testTextFormatConstant(): void {
    self::assertSame('text', PortfolioSettings::TEXT_FORMAT);
  }

  public function testCacheContexts(): void {
    $settings = $this->buildSettings([]);

    self::assertSame(['languages:language_interface'], $settings->getCacheContexts());
  }

  public function testCacheTags(): void {
    $settings = $this->buildSettings([]);

    self::assertSame(['app_portfolio.settings'], $settings->getCacheTags());
  }

  /**
   * @param array<string, mixed> $store_data
   */
  private function buildSettings(array $store_data): PortfolioSettings {
    $store = $this->prophesize(LanguageAwareStore::class);

    foreach ($store_data as $key => $value) {
      $store->get($key, Argument::any())->willReturn($value);
    }

    $store->get('description', 'The portfolio page description.')->willReturn(
      $store_data['description'] ?? 'The portfolio page description.',
    );

    return $this->buildSettingsWithStore($store->reveal());
  }

  private function buildSettingsWithStore(LanguageAwareStore $store): PortfolioSettings {
    $factory = $this->prophesize(LanguageAwareFactory::class);
    $factory->get('app_portfolio.settings', NULL)->willReturn($store);

    $route_match = $this->prophesize(RouteMatchInterface::class);
    $route_match->getParameter('key_value_language_aware_code')->willReturn(NULL);

    return new PortfolioSettings(
      $factory->reveal(),
      $route_match->reveal(),
    );
  }

}
