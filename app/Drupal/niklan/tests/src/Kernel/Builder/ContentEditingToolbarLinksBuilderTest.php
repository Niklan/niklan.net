<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Build;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Menu\LocalTaskManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Security\DoTrustedCallbackTrait;
use Drupal\Core\Url;
use Drupal\niklan\Builder\ContentEditingToolbarLinksBuilder;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Prophecy\Argument;

/**
 * Provides a test for content editing toolbar links.
 *
 * @covers \Drupal\niklan\Builder\ContentEditingToolbarLinksBuilder
 */
final class ContentEditingToolbarLinksBuilderTest extends NiklanTestBase {

  use DoTrustedCallbackTrait;

  /**
   * Tests that links are built as expected.
   */
  public function testBuildLinks(): void {
    $cacheable_metadata = new CacheableMetadata();
    $cacheable_metadata->addCacheTags(['foo_bar']);

    $local_task_manager = $this->prophesize(LocalTaskManagerInterface::class);
    $local_task_manager->getLocalTasks(Argument::cetera())->willReturn([
      'tabs' => [
        'foo' => [
          '#theme' => 'menu_local_task',
          '#link' => [
            'title' => 'Link Foo',
            'url' => Url::fromRoute('entity.entity_test.canonical', [
              'entity_test' => '1',
            ]),
          ],
          '#active' => TRUE,
          '#weight' => 1,
          '#access' => TRUE,
        ],
      ],
      'route_name' => $this->randomMachineName(),
      'cacheability' => $cacheable_metadata,
    ]);

    $route_match = $this->prophesize(RouteMatchInterface::class);

    $links_builder = new ContentEditingToolbarLinksBuilder(
      $local_task_manager->reveal(),
      $route_match->reveal(),
    );

    $links = $this->doTrustedCallback(
      [$links_builder, 'buildLinks'],
      [],
      '%s should be listed as trusted callback',
    );

    self::assertArrayHasKey('foo', $links['#links']);
    self::assertEquals('Link Foo', $links['#links']['foo']['title']);
    $foo_url = $links['#links']['foo']['url'];
    \assert($foo_url instanceof Url);
    $foo_classes = $foo_url->getOption('attributes')['class'];
    self::assertContains('toolbar-icon', $foo_classes);
    self::assertContains(
      'toolbar-icon--route-name-entity-entity-test-canonical',
      $foo_classes,
    );
    self::assertContains('toolbar-icon--entity-route', $foo_classes);
    self::assertContains(
      'toolbar-icon--entity-type-id-entity-test',
      $foo_classes,
    );
    self::assertContains(
      'toolbar-icon--entity-route-type-canonical',
      $foo_classes,
    );
    self::assertContains('foo_bar', $links['#cache']['tags']);
  }

}
