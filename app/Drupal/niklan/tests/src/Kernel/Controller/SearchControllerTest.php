<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Search\Controller\Search;
use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\niklan\Search\Data\EntitySearchResults;
use Drupal\niklan\Search\Repository\EntitySearch;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a test for search controller.
 *
 * @covers \Drupal\niklan\Search\Controller\Search
 */
final class SearchControllerTest extends NiklanTestBase {

  /**
   * Tests page title generation.
   */
  public function testPageTitle(): void {
    $query = $this->prophesize(ParameterBagInterface::class);
    $query->get('q')->willReturn(NULL, 'foo');

    $request = $this->prophesize(Request::class);
    $request->query = $query->reveal();
    $request = $request->reveal();

    $controller = $this
      ->container
      ->get('class_resolver')
      ->getInstanceFromDefinition(Search::class);

    // With 'NULL' query.
    $title = $controller->pageTitle($request);
    // With 'foo' query.
    $title_second = $controller->pageTitle($request);

    // The page title should be different for empty request and with keys.
    self::assertNotEquals($title, $title_second);
  }

  /**
   * Tests that page contents built as expected without any keys.
   */
  public function testBuildPageContentWithoutKeys(): void {
    $controller = $this
      ->container
      ->get('class_resolver')
      ->getInstanceFromDefinition(Search::class);

    $result = $controller->buildPageContent(NULL);

    self::assertArrayHasKey('#theme', $result);
    self::assertArrayNotHasKey('#results', $result);
  }

  /**
   * Tests that page contents built as expected without results found.
   */
  public function testBuildPageContentWithoutResults(): void {
    $search_results = new EntitySearchResults([], 0);
    $entity_search = $this->buildEntitySearch($search_results);
    $this->container->set('niklan.search.global', $entity_search);

    $controller = Search::create($this->container);

    $result = $controller->doSearch('Hello');

    self::assertArrayHasKey('#theme', $result);
    self::assertArrayNotHasKey('#results', $result);
  }

  /**
   * Tests that page contents built as expected with results found.
   */
  public function testBuildPageContentWithResults(): void {
    $search_results = new EntitySearchResults([
      new EntitySearchResult('node', 1, 'ru'),
    ], 1);
    $entity_search = $this->buildEntitySearch($search_results);
    $this->container->set('niklan.search.global', $entity_search);

    $controller = Search::create($this->container);

    $result = $controller->doSearch('Hello');

    self::assertArrayHasKey('#theme', $result);
    self::assertArrayHasKey('#results', $result);
  }

  /**
   * Tests that page callback response properly.
   */
  public function testPage(): void {
    $query = $this->prophesize(ParameterBagInterface::class);
    $query->get('q')->willReturn(NULL);

    $request = $this->prophesize(Request::class);
    $request->query = $query->reveal();

    $search_results = new EntitySearchResults([
      new EntitySearchResult('node', 1, 'ru'),
    ], 1);
    $entity_search = $this->buildEntitySearch($search_results);
    $this->container->set('niklan.search.global', $entity_search);

    $controller = Search::create($this->container);
    $result = $controller->page($request->reveal());

    self::assertArrayHasKey('#theme', $result);
    self::assertArrayHasKey('#header', $result);
    self::assertArrayHasKey('#content', $result);
  }

  /**
   * Builds an entity search prophecy.
   *
   * @param \Drupal\niklan\Search\Data\EntitySearchResults $results
   *   The entity search results.
   */
  protected function buildEntitySearch(EntitySearchResults $results): EntitySearch {
    $entity_search = $this->prophesize(EntitySearch::class);
    $entity_search->search(Argument::any())->willReturn($results);

    return $entity_search->reveal();
  }

}
