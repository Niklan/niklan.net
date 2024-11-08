<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Toolbar;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContentEditingToolbar implements ContainerInjectionInterface {

  protected array $items = [];
  protected RouteMatchInterface $routeMatch;

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $instance = new self();
    $instance->routeMatch = $container->get('current_route_match');

    return $instance;
  }

  protected function prepareContentEditingToolbar(): void {
    $this->items['niklan_content_editing'] = [
      '#type' => 'toolbar_item',
      'tab' => [
        '#type' => 'link',
        '#title' => new TranslatableMarkup('Edit'),
        '#url' => Url::fromRoute('<current>'),
        '#attributes' => [
          'title' => new TranslatableMarkup('Edit'),
          'class' => ['toolbar-icon', 'toolbar-icon-content-editing'],
        ],
      ],
      'tray' => [
        '#heading' => new TranslatableMarkup('Edit'),
        '#weight' => 0,
      ],
      '#weight' => -11,
      '#attached' => [
        'library' => ['niklan/content-editing.toolbar'],
      ],
      '#cache' => [
        'contexts' => ['route'],
      ],
    ];

    $this->items['niklan_content_editing']['tray']['user_links'] = [
      '#lazy_builder' => [
        'Drupal\niklan\Navigation\Toolbar\ContentEditingToolbarLinksBuilder:buildLinks',
        [],
      ],
      '#create_placeholder' => TRUE,
    ];
  }

  public function __invoke(): array {
    $supported_content_routes = [
      'entity.node.canonical',
      'entity.taxonomy_term.canonical',
      'entity.user.canonical',
    ];

    if (\in_array($this->routeMatch->getRouteName(), $supported_content_routes)) {
      $this->prepareContentEditingToolbar();
    }
    else {
      // If current route is not supported we still add element with cache
      // context. This will cover cases when after clearing the cache the first
      // opened page doesn't contain this element and because of this the others
      // will not contain it as well.
      $this->items['niklan_content_editing'] = [
        '#cache' => [
          'contexts' => ['route'],
        ],
      ];
    }

    return $this->items;
  }

}
