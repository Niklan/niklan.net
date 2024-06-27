<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Block;

use Drupal\Core\Url;
use Drupal\niklan\Plugin\Block\SidebarSearchFormBlock;

/**
 * Provides a test for search form in sidebar.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Block\SidebarSearchFormBlock
 */
final class SidebarSearchFormBlockTest extends BlockTestBase {

  /**
   * Tests that block works as expected.
   */
  public function testBlock(): void {
    $block = $this
      ->blockManager
      ->createInstance('niklan_node_sidebar_search_form');
    \assert($block instanceof SidebarSearchFormBlock);
    $build = $block->build();
    $this->render($build);

    $search_action_url = Url::fromRoute('niklan.search_page')->toString();
    self::assertRaw($search_action_url);
    self::assertCount(1, $this->cssSelect('.sidebar-search-form input'));
    self::assertCount(1, $this->cssSelect('.sidebar-search-form button'));
  }

}
