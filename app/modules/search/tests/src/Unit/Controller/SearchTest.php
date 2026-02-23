<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Controller;

use Drupal\app_search\Controller\Search;
use Drupal\app_search\Data\EntitySearchResult;
use Drupal\app_search\Data\EntitySearchResults;
use Drupal\app_search\Repository\EntitySearch;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(Search::class)]
final class SearchTest extends UnitTestCase {

  public function testEmptyQuery(): void {
    $controller = $this->buildController(new EntitySearchResults([]));

    $result = $controller(new Request());

    self::assertSame('app_search_results', $result['#theme']);
    self::assertSame('', $result['#query']);
    self::assertSame([], $result['#results']);
  }

  public function testNoResults(): void {
    $controller = $this->buildController(new EntitySearchResults([], 0));

    $result = $controller(Request::create('/search', 'GET', ['q' => 'nonexistent']));

    self::assertSame('nonexistent', $result['#query']);
    self::assertSame([], $result['#results']);
  }

  public function testWithResults(): void {
    $search_results = new EntitySearchResults(
      [new EntitySearchResult('node', '1', 'ru')],
      1,
    );

    $node = $this->prophesize(NodeInterface::class);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->loadMultiple(['1'])->willReturn([$node->reveal()]);
    $storage->load('1')->willReturn($node->reveal());

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);
    $view_builder->view($node->reveal(), 'search_result')->willReturn(['#markup' => 'result']);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('node')->willReturn($storage->reveal());
    $entity_type_manager->getViewBuilder('node')->willReturn($view_builder->reveal());

    $controller = $this->buildController(
      $search_results,
      $entity_type_manager->reveal(),
    );

    $result = $controller(Request::create('/search', 'GET', ['q' => 'drupal']));

    self::assertSame('drupal', $result['#query']);
    self::assertCount(1, $result['#results']);
  }

  public function testCacheMetadata(): void {
    $controller = $this->buildController(new EntitySearchResults([]));

    $result = $controller(new Request());

    self::assertContains('url.query_args:q', $result['#cache']['contexts']);
    self::assertContains('url.query_args.pagers:0', $result['#cache']['contexts']);
    self::assertContains('search_api_list:global_index', $result['#cache']['tags']);
  }

  private function buildController(EntitySearchResults $search_results, ?EntityTypeManagerInterface $entity_type_manager = NULL): Search {
    $entity_search = $this->prophesize(EntitySearch::class);
    $entity_search->search(Argument::any())->willReturn($search_results);

    if (!$entity_type_manager) {
      $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
      $storage = $this->prophesize(EntityStorageInterface::class);
      $entity_type_manager->getStorage(Argument::any())->willReturn($storage->reveal());
      $entity_type_manager = $entity_type_manager->reveal();
    }

    $pager_manager = $this->prophesize(PagerManagerInterface::class);
    $pager_manager->findPage()->willReturn(0);
    $pager_manager->createPager(Argument::cetera())->willReturn(NULL);

    return new Search(
      $entity_search->reveal(),
      $entity_type_manager,
      $pager_manager->reveal(),
    );
  }

}
