<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\node\NodeInterface;
use Drupal\Tests\niklan\Traits\ParagraphHeadingTrait;

/**
 * Provides a test for TOC block.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Block\TocBlock
 */
final class TocBlockTest extends BlockTestBase {

  use ParagraphHeadingTrait;

  /**
   * Prepares prophecy for entity reference field.
   *
   * @param array $paragraphs
   *   An array with paragraph items.
   */
  protected function prepareFieldItemList(array $paragraphs = []): EntityReferenceRevisionsFieldItemList {
    $items = $this->prophesize(EntityReferenceRevisionsFieldItemList::class);
    $items->referencedEntities()->willReturn($paragraphs);

    return $items->reveal();
  }

  /**
   * Tests that block works properly when node is not found.
   */
  public function testBlockWithoutNode(): void {
    $block = $this->blockManager->createInstance('niklan_node_toc');
    \assert($block instanceof BlockPluginInterface);
    $build = $block->build();
    $this->render($build);

    self::assertCount(0, $this->cssSelect('.toc'));
  }

  /**
   * Tests that block works properly when node has not heading paragraphs.
   */
  public function testBlockWithoutHeadings(): void {
    $node = $this->prophesize(NodeInterface::class);
    $node->get('field_content')->willReturn($this->prepareFieldItemList());
    $node->getCacheContexts()->willReturn([]);
    $node->getCacheTags()->willReturn([]);
    $node->getCacheMaxAge()->willReturn(Cache::PERMANENT);
    $node->getType()->willReturn('node');

    $route_match = $this->prophesize(RouteMatchInterface::class);
    $route_match->getParameter('node')->willReturn($node->reveal());
    $route_match->getRouteObject()->willReturn(NULL);
    $route_match->getRouteName()->willReturn(NULL);

    $this->container->set('current_route_match', $route_match->reveal());

    $block = $this->blockManager->createInstance('niklan_node_toc');
    \assert($block instanceof BlockPluginInterface);
    $build = $block->build();
    $this->render($build);

    self::assertCount(0, $this->cssSelect('.toc'));
  }

  /**
   * Tests that block works properly when headings are found.
   */
  public function testBlockWithHeadings(): void {
    $items = [
      $this->prepareHeadingParagraph('Foo', 'h2'),
      $this->prepareHeadingParagraph('Bar', 'h3'),
    ];

    $node = $this->prophesize(NodeInterface::class);
    $node->get('field_content')->willReturn($this->prepareFieldItemList($items));
    $node->getCacheContexts()->willReturn([]);
    $node->getCacheTags()->willReturn([]);
    $node->getCacheMaxAge()->willReturn(Cache::PERMANENT);
    $node->getType()->willReturn('node');

    $route_match = $this->prophesize(RouteMatchInterface::class);
    $route_match->getParameter('node')->willReturn($node->reveal());
    $route_match->getRouteObject()->willReturn(NULL);
    $route_match->getRouteName()->willReturn(NULL);

    $this->container->set('current_route_match', $route_match->reveal());

    $block = $this->blockManager->createInstance('niklan_node_toc');
    \assert($block instanceof BlockPluginInterface);
    $build = $block->build();
    $this->render($build);

    self::assertCount(1, $this->cssSelect('.toc'));
    self::assertCount(2, $this->cssSelect('.toc__link'));
    self::assertRaw('Foo');
    self::assertRaw('Bar');
  }

  /**
   * Tests that cache contexts are properly built.
   */
  public function testCacheContexts(): void {
    $block = $this->blockManager->createInstance('niklan_node_toc');
    \assert($block instanceof BlockPluginInterface);

    self::assertContains('url.path', $block->getCacheContexts());
  }

}
