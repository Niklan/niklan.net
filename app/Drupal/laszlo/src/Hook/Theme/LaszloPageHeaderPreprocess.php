<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class LaszloPageHeaderPreprocess implements ContainerInjectionInterface {

  public function __construct(
    private ConfigFactoryInterface $configFactory,
    private ThemeManagerInterface $themeManager,
    private MenuLinkTreeInterface $menuLinkTree,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ConfigFactoryInterface::class),
      $container->get(ThemeManagerInterface::class),
      $container->get(MenuLinkTreeInterface::class),
    );
  }

  private function prepareBrandingVariables(array &$variables): void {
    $cache = CacheableMetadata::createFromRenderArray($variables);

    $site_settings = $this->configFactory->getEditable('system.site');
    $cache->addCacheableDependency($site_settings);

    $logo_path = $this->themeManager->getActiveTheme()->getPath() . '/logo.svg';

    $variables['site_logo_svg'] = \file_exists($logo_path) ? $logo_path : NULL;
    $variables['site_name'] = $site_settings->get('name');
    $variables['site_slogan'] = $site_settings->get('slogan');
    $variables['site_url'] = Url::fromRoute('<front>')->toString();

    $cache->applyTo($variables);
  }

  private function prepareNavigationVariables(array &$variables): void {
    $cache = CacheableMetadata::createFromRenderArray($variables);

    $tree = $this->menuLinkTree->load('main', new MenuTreeParameters());
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $variables['navigation'] = \array_map(
      callback: static fn (MenuLinkTreeElement $element): array => [
        'label' => $element->link->getTitle(),
        'url' => $element->link->getUrlObject()->toString(),
      ],
      array: $this->menuLinkTree->transform($tree, $manipulators),
    );

    $cache->applyTo($variables);
  }

  public function __invoke(array &$variables): void {
    $this->prepareBrandingVariables($variables);
    $this->prepareNavigationVariables($variables);
  }

}
