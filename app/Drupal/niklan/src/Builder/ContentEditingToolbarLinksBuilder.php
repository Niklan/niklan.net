<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SortArray;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Menu\LocalTaskManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;

/**
 * Provides a builder for content editing toolbar links.
 */
final class ContentEditingToolbarLinksBuilder implements TrustedCallbackInterface {

  /**
   * Constructs a new ContentEditingToolbarLinksBuilder object.
   *
   * @param \Drupal\Core\Menu\LocalTaskManagerInterface $localTaskManager
   *   The local task manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   */
  public function __construct(
    protected LocalTaskManagerInterface $localTaskManager,
    protected RouteMatchInterface $routeMatch,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['buildLinks'];
  }

  /**
   * Builds menu links for content editing toolbar tab.
   *
   * @return array
   *   An array with menu.
   */
  public function buildLinks(): array {
    $local_tasks = $this->localTaskManager->getLocalTasks(
      $this->routeMatch->getRouteName(),
    );

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
