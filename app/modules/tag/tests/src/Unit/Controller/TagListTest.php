<?php

declare(strict_types=1);

namespace Drupal\Tests\app_tag\Unit\Controller;

use Drupal\app_contract\Contract\Tag\TagUsageStatistics;
use Drupal\app_tag\Controller\TagList;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TagList::class)]
final class TagListTest extends UnitTestCase {

  public function testThemeName(): void {
    $controller = $this->buildController();

    $result = $controller();

    self::assertSame('app_tag_list', $result['#theme']);
  }

  public function testEmptyItems(): void {
    $controller = $this->buildController();

    $result = $controller();

    self::assertSame([], $result['#items']);
  }

  public function testWithItems(): void {
    $term = $this->prophesize(TermInterface::class);

    $statistics = $this->prophesize(TagUsageStatistics::class);
    $statistics->usage()->willReturn([
      1 => (object) ['tid' => 1, 'count' => 5],
      2 => (object) ['tid' => 2, 'count' => 3],
    ]);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->loadMultiple([1, 2])->willReturn([
      $term->reveal(),
      $term->reveal(),
    ]);

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);
    $view_builder->view($term->reveal(), 'teaser')->willReturn(['#markup' => 'rendered']);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('taxonomy_term')->willReturn($storage->reveal());
    $entity_type_manager->getViewBuilder('taxonomy_term')->willReturn($view_builder->reveal());

    $controller = new TagList(
      $entity_type_manager->reveal(),
      $statistics->reveal(),
    );

    $result = $controller();

    self::assertCount(2, $result['#items']);
  }

  private function buildController(): TagList {
    $statistics = $this->prophesize(TagUsageStatistics::class);
    $statistics->usage()->willReturn([]);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->loadMultiple([])->willReturn([]);

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('taxonomy_term')->willReturn($storage->reveal());
    $entity_type_manager->getViewBuilder('taxonomy_term')->willReturn($view_builder->reveal());

    return new TagList(
      $entity_type_manager->reveal(),
      $statistics->reveal(),
    );
  }

}
