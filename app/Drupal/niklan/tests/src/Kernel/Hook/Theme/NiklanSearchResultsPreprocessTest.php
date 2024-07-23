<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Hook\Theme;

use Drupal\niklan\Data\EntitySearchResult;
use Drupal\niklan\Data\EntitySearchResults;
use Drupal\niklan\Hook\Theme\NiklanSearchResultsPreprocess;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Tests template_preprocess_niklan_search_results() implementation.
 *
 * @coversDefaultClass \Drupal\niklan\Hook\Theme\NiklanSearchResultsPreprocess
 */
final class NiklanSearchResultsPreprocessTest extends NiklanTestBase {

  protected NiklanSearchResultsPreprocess $implementation;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node'];

  /**
   * Tests implementation when no search results provided.
   */
  public function testNoResultsProvided(): void {
    $variables = [];
    $this->implementation->__invoke($variables);

    self::assertEquals([], $variables);
  }

  /**
   * Tests implementation when results are wrong type.
   */
  public function testResultsAreWrongType(): void {
    $variables = ['results' => []];
    $this->implementation->__invoke($variables);

    self::assertEquals(['results' => []], $variables);
  }

  /**
   * Tests that results are built.
   */
  public function testResultsBuild(): void {
    NodeType::create([
      'type' => 'page',
      'name' => 'Basic page',
    ])->save();

    Node::create([
      'type' => 'page',
      'title' => 'Foo bar',
    ])->save();

    $result_item = new EntitySearchResult('node', 1, 'ru');
    $result_set = new EntitySearchResults([$result_item], 1);
    $variables = ['results' => $result_set];
    $this->implementation->__invoke($variables);

    self::assertCount(1, $variables['results']);
    self::assertArrayHasKey('#node', $variables['results'][0]);
    self::assertEquals('search_result', $variables['results'][0]['#view_mode']);
    self::assertArrayHasKey('pager', $variables);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->implementation = NiklanSearchResultsPreprocess::create($this->container);

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
  }

}
