<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme\PropsAlter;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PageHeaderPropsAlter implements ContainerInjectionInterface {

  public function __construct(
    private MenuLinkTreeInterface $menuLinkTree,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(MenuLinkTreeInterface::class),
    );
  }

  public function __invoke(array $props): array {
    $tree = $this->menuLinkTree->load('main', new MenuTreeParameters());
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $props['navigation'] = \array_map(
      callback: static function (MenuLinkTreeElement $element): array {
        $plugin_definition = $element->link->getPluginDefinition();
        \assert(\is_array($plugin_definition));

        return [
          'label' => $element->link->getTitle(),
          'url' => $element->link->getUrlObject()->toString(),
          'icon' => $plugin_definition['metadata']['main_navigation_icon'] ?? NULL,
          'active_trail_pattern' => $plugin_definition['metadata']['active_trail_pattern'] ?? NULL,
        ];
      },
      array: $this->menuLinkTree->transform($tree, $manipulators),
    );

    return $props;
  }

}
