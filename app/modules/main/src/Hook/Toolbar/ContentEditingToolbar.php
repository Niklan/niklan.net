<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Toolbar;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;

#[Hook('toolbar')]
final class ContentEditingToolbar {

  private array $items = [];

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly TranslationInterface $stringTranslation,
  ) {}

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
      $this->items['app_main_content_editing'] = [
        '#cache' => [
          'contexts' => ['route'],
        ],
      ];
    }

    return $this->items;
  }

  protected function prepareContentEditingToolbar(): void {
    $this->items['app_main_content_editing'] = [
      '#type' => 'toolbar_item',
      'tab' => [
        '#type' => 'link',
        '#title' => $this->stringTranslation->translate('Edit'),
        '#url' => Url::fromRoute('<current>'),
        '#attributes' => [
          'title' => $this->stringTranslation->translate('Edit'),
          'class' => ['toolbar-icon', 'toolbar-icon-content-editing'],
        ],
      ],
      'tray' => [
        '#heading' => $this->stringTranslation->translate('Edit'),
        '#weight' => 0,
      ],
      '#weight' => -11,
      '#attached' => [
        'library' => ['app_main/content-editing.toolbar'],
      ],
      '#cache' => [
        'contexts' => ['route'],
      ],
    ];

    $this->items['app_main_content_editing']['tray']['user_links'] = [
      '#lazy_builder' => [
        // @phpstan-ignore-next-line renderCallback.nonCallableCallback
        'Drupal\app_main\Navigation\Toolbar\ContentEditingToolbarLinksBuilder:buildLinks',
        [],
      ],
      '#create_placeholder' => TRUE,
    ];
  }

}
