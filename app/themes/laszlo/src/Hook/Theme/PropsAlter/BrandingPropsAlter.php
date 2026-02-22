<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme\PropsAlter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class BrandingPropsAlter implements ContainerInjectionInterface {

  public function __construct(
    private ConfigFactoryInterface $configFactory,
    private ThemeManagerInterface $themeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ConfigFactoryInterface::class),
      $container->get(ThemeManagerInterface::class),
    );
  }

  public function __invoke(array $props): array {
    $site_settings = $this->configFactory->get('system.site');

    $logo_path = $this->themeManager->getActiveTheme()->getPath() . '/logo.svg';

    $props['logo_svg'] = \file_exists($logo_path) ? $logo_path : NULL;
    $props['name'] = $site_settings->get('name');
    $props['slogan'] = $site_settings->get('slogan');
    $props['url'] = Url::fromRoute('<front>')->toString();

    return $props;
  }

}
