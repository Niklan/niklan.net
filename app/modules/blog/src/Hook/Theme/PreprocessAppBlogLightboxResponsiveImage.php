<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\photoswipe\PhotoswipeAssetsManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Hook('preprocess_app_blog_lightbox_responsive_image')]
final readonly class PreprocessAppBlogLightboxResponsiveImage {

  public function __construct(
    #[Autowire(service: 'photoswipe.assets_manager')]
    private PhotoswipeAssetsManagerInterface $photoswipeAssetsManager,
  ) {}

  public function __invoke(array &$variables): void {
    $this->photoswipeAssetsManager->attach($variables);
  }

}
