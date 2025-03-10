<?php

declare(strict_types=1);

namespace Drupal\niklan\Navigation\Toolbar;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SortArray;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Menu\LocalTaskManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;

final class ContentEditingToolbarLinksBuilder implements TrustedCallbackInterface {

  public function __construct(
    protected LocalTaskManagerInterface $localTaskManager,
    protected RouteMatchInterface $routeMatch,
  ) {}

  public function buildLinks(): array {
    $route_name = $this->routeMatch->getRouteName();

    if (!$route_name) {
      return [];
    }

    $local_tasks = $this->localTaskManager->getLocalTasks($route_name);

    $links = \array_map(function (array $tab): array {
      $url = $tab['#link']['url'];
      \assert($url instanceof Url);
      // Replace tab classes by our own.
      $url->setOption('attributes', [
        'class' => $this->buildLinkClasses($url),
      ]);

      return [
        'title' => $tab['#link']['title'],
        'url' => $url,
        'weight' => $tab['#weight'],
      ];
    }, $local_tasks['tabs']);

    \uasort($links, [SortArray::class, 'sortByWeightElement']);

    $build = [
      '#theme' => 'links__toolbar',
      '#links' => $links,
      '#attributes' => [
        'class' => ['toolbar-menu'],
      ],
    ];

    $cache = $local_tasks['cacheability'];
    \assert($cache instanceof CacheableMetadata);
    $cache->applyTo($build);

    return $build;
  }

  #[\Override]
  public static function trustedCallbacks(): array {
    return ['buildLinks'];
  }

  /**
   * Builds a CSS classes for link.
   *
   * @param \Drupal\Core\Url $url
   *   The URL object.
   *
   * @return string[]
   *   An array with classes.
   */
  protected function buildLinkClasses(Url $url): array {
    $classes = [
      'toolbar-icon',
    ];

    $route_name = $url->getRouteName();
    $classes[] = 'toolbar-icon--route-name-' . Html::getClass(\str_replace('.', '-', $route_name));

    // If route is entity one, provide universal route classes.
    if (\stristr($route_name, 'entity.')) {
      [, $entity_type_id, $route_id] = \explode('.', $route_name);
      $classes[] = 'toolbar-icon--' . Html::getClass('entity-route');
      $classes[] = 'toolbar-icon--' . Html::getClass('entity-type-id-' . $entity_type_id);
      $classes[] = 'toolbar-icon--' . Html::getClass('entity-route-type-' . $route_id);
    }

    return $classes;
  }

}
