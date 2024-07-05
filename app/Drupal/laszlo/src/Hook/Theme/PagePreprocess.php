<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PagePreprocess implements ContainerInjectionInterface {

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

  public function __invoke(array &$variables): void {
    $this->prepareHeader($variables);
  }

  private function prepareHeader(array &$variables): void {
    $variables['header'] = [
      '#type' => 'component',
      '#component' => 'laszlo:header',
      '#slots' => [
        'branding' => $this->prepareBranding(),
        'navigation' => $this->prepareNavigation(),
        'search' => $this->prepareSearch(),
      ],
      '#cache' => [
        'keys' => ['laszlo', 'page', 'header'],
      ],
    ];
  }

  private function prepareBranding(): array {
    $cache = new CacheableMetadata();

    $site_settings = $this->configFactory->getEditable('system.site');
    $cache->addCacheableDependency($site_settings);

    $logo_path = $this->themeManager->getActiveTheme()->getPath() . '/logo.svg';

    $build = [
      '#type' => 'component',
      '#component' => 'laszlo:branding',
      '#props' => [
        'site_logo_svg' => \file_exists($logo_path) ? $logo_path : NULL,
        'site_name' => $site_settings->get('name'),
        'site_slogan' => $site_settings->get('slogan'),
        'url' => Url::fromRoute('<front>')->toString(),
      ],
    ];

    $cache->applyTo($build);

    return $build;
  }

  private function prepareNavigation(): array {
    $tree = $this->menuLinkTree->load('main', new MenuTreeParameters());
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $this->menuLinkTree->transform($tree, $manipulators);

    return [
      '#type' => 'component',
      '#component' => 'laszlo:main-navigation',
      '#slots' => [
        'items' => \array_map($this->prepareNavigationItem(...), $tree),
      ],
    ];
  }

  private function prepareNavigationItem(MenuLinkTreeElement $element): array {
    return [
      '#type' => 'component',
      '#component' => 'laszlo:main-navigation-item',
      '#props' => [
        'label' => $element->link->getTitle(),
        'url' => $element->link->getUrlObject()->toString(),
      ],
    ];
  }

  private function prepareSearch(): array {
    return [
      '#type' => 'component',
      '#component' => 'laszlo:search-bar',
      '#props' => [
        'placeholder' => new TranslatableMarkup('Site search'),
      ],
    ];
  }

}
