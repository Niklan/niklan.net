<?php

declare(strict_types=1);

namespace Drupal\Tests\app_portfolio\Unit\Controller;

use Drupal\app_contract\Contract\LanguageAwareStore\LanguageAwareFactory;
use Drupal\app_portfolio\Controller\PortfolioList;
use Drupal\app_portfolio\Repository\PortfolioSettings;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\app_contract\Contract\LanguageAwareStore\LanguageAwareStore;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;

#[CoversClass(PortfolioList::class)]
final class PortfolioListTest extends UnitTestCase {

  public function testThemeName(): void {
    $controller = $this->buildController();

    $result = $controller();

    self::assertSame('app_portfolio_list', $result['#theme']);
  }

  public function testDescription(): void {
    $controller = $this->buildController(description: 'Test description');

    $result = $controller();

    self::assertSame('processed_text', $result['#description']['#type']);
    self::assertSame('Test description', $result['#description']['#text']);
    self::assertSame('text', $result['#description']['#format']);
  }

  public function testEmptyItems(): void {
    $controller = $this->buildController();

    $result = $controller();

    self::assertSame([], $result['#items']);
  }

  public function testWithItems(): void {
    $node = $this->prophesize(NodeInterface::class);

    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(FALSE)->willReturn($query->reveal());
    $query->condition(Argument::cetera())->willReturn($query->reveal());
    $query->sort(Argument::cetera())->willReturn($query->reveal());
    $query->execute()->willReturn([1, 2]);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->getQuery()->willReturn($query->reveal());
    $storage->loadMultiple([1, 2])->willReturn([
      $node->reveal(),
      $node->reveal(),
    ]);

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);
    $view_builder->view(Argument::any(), 'teaser')->willReturn(['#markup' => 'rendered']);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('node')->willReturn($storage->reveal());
    $entity_type_manager->getViewBuilder('node')->willReturn($view_builder->reveal());

    $controller = $this->buildController(
      entity_type_manager: $entity_type_manager->reveal(),
    );

    $result = $controller();

    self::assertCount(2, $result['#items']);
  }

  private function buildController(?EntityTypeManagerInterface $entity_type_manager = NULL, string $description = 'The portfolio page description.'): PortfolioList {
    if (!$entity_type_manager) {
      $query = $this->prophesize(QueryInterface::class);
      $query->accessCheck(Argument::any())->willReturn($query->reveal());
      $query->condition(Argument::cetera())->willReturn($query->reveal());
      $query->sort(Argument::cetera())->willReturn($query->reveal());
      $query->execute()->willReturn([]);

      $storage = $this->prophesize(EntityStorageInterface::class);
      $storage->getQuery()->willReturn($query->reveal());
      $storage->loadMultiple([])->willReturn([]);

      $view_builder = $this->prophesize(EntityViewBuilderInterface::class);

      $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
      $entity_type_manager->getStorage('node')->willReturn($storage->reveal());
      $entity_type_manager->getViewBuilder('node')->willReturn($view_builder->reveal());
      $entity_type_manager = $entity_type_manager->reveal();
    }

    $settings = $this->buildSettings($description);

    return new PortfolioList($entity_type_manager, $settings);
  }

  private function buildSettings(string $description): PortfolioSettings {
    $store = $this->prophesize(LanguageAwareStore::class);
    $store->get('description', 'The portfolio page description.')->willReturn($description);

    $factory = $this->prophesize(LanguageAwareFactory::class);
    $factory->get('app_portfolio.settings', NULL)->willReturn($store->reveal());

    $route_match = $this->prophesize(RouteMatchInterface::class);
    $route_match->getParameter('key_value_language_aware_code')->willReturn(NULL);

    return new PortfolioSettings(
      $factory->reveal(),
      $route_match->reveal(),
    );
  }

}
