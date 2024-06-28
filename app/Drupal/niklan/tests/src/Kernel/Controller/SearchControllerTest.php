<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Controller\SearchController;
use Drupal\niklan\Data\EntitySearchResult;
use Drupal\niklan\Data\EntitySearchResults;
use Drupal\niklan\Search\EntitySearchInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a test for search controller.
 *
 * @covers \Drupal\niklan\Controller\SearchController
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
      ->getInstanceFromDefinition(SearchController::class);

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
      ->getInstanceFromDefinition(SearchController::class);

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

    $controller = SearchController::create($this->container);

    $result = $controller->buildPageContent('Hello');

    self::assertArrayHasKey('#theme', $result);
    self::assertArrayNotHasKey('#results', $result);
  }

  /**
   * Builds an entity search prophecy.
   *
   * @param \Drupal\niklan\Data\EntitySearchResults $results
   *   The entity search results.
   */
  protected function buildEntitySearch(EntitySearchResults $results): EntitySearchInterface {
    $entity_search = $this->prophesize(EntitySearchInterface::class);
    $entity_search->search(Argument::any())->willReturn($results);

    return $entity_search->reveal();
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

    $controller = SearchController::create($this->container);

    $result = $controller->buildPageContent('Hello');

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

    $controller = SearchController::create($this->container);
    $result = $controller->page($request->reveal());

    self::assertArrayHasKey('#theme', $result);
    self::assertArrayHasKey('#header', $result);
    self::assertArrayHasKey('#content', $result);
  }

}
