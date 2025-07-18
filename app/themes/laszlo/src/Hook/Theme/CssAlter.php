<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class CssAlter implements ContainerInjectionInterface {

  public function __construct(
    private ThemeManagerInterface $themeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ThemeManagerInterface::class),
    );
  }

  private function forcePrioritizedStyles(array &$css): void {
    $active_theme = $this->themeManager->getActiveTheme();
    $layers_path = $active_theme->getPath() . '/assets/css/00-setting/layer.css';

    if (!\array_key_exists($layers_path, $css)) {
      return;
    }

    // Workaround for #3489336.
    $css[$layers_path]['group'] = \CSS_AGGREGATE_DEFAULT;
  }

  public function __invoke(array &$css, AttachedAssetsInterface $assets, LanguageInterface $language): void {
    $this->forcePrioritizedStyles($css);
  }

}
