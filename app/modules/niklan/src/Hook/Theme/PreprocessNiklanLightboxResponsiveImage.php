<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Image\ImageFactory;
use Drupal\image\ImageStyleInterface;
use Drupal\photoswipe\PhotoswipeAssetsManagerInterface;
use Drupal\responsive_image\ResponsiveImageStyleInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Hook('preprocess_niklan_lightbox_responsive_image')]
final readonly class PreprocessNiklanLightboxResponsiveImage {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private ImageFactory $imageFactory,
    #[Autowire(service: 'photoswipe.assets_manager')]
    private PhotoswipeAssetsManagerInterface $photoswipeAssetsManager,
  ) {}

  private function prepareResponsiveImage(array &$variables): void {
    \assert($variables['thumbnail_responsive_image_style_id']);
    $storage = $this->entityTypeManager->getStorage('responsive_image_style');
    $style = $storage->load($variables['thumbnail_responsive_image_style_id']);

    if ($style instanceof ResponsiveImageStyleInterface) {
      $variables['responsive_image'] = [
        '#type' => 'responsive_image',
        '#responsive_image_style_id' => $variables['thumbnail_responsive_image_style_id'],
      ];
    }
    else {
      $variables['responsive_image'] = [
        '#theme' => 'image',
      ];
    }

    $variables['responsive_image']['#uri'] = $variables['uri'];

    if (\array_key_exists('alt', $variables)) {
      $variables['responsive_image']['#attributes']['alt'] = $variables['alt'];
    }

    if (!\array_key_exists('title', $variables)) {
      return;
    }

    $variables['responsive_image']['#attributes']['title'] = $variables['title'];
  }

  private function prepareFullImageUrl(array &$variables): void {
    \assert($variables['lightbox_image_style_id']);
    $storage = $this->entityTypeManager->getStorage('image_style');
    $style = $storage->load($variables['lightbox_image_style_id']);
    \assert($style instanceof ImageStyleInterface);
    $variables['full_image_url'] = $style->buildUrl($variables['uri']);
    // Prepare size for zoom aspect ratio.
    $image = $this->imageFactory->get($style->buildUri($variables['uri']));
    $variables['size'] = "{$image->getWidth()}x{$image->getHeight()}";
  }

  public function __invoke(array &$variables): void {
    $this->photoswipeAssetsManager->attach($variables);
    $this->prepareResponsiveImage($variables);
    $this->prepareFullImageUrl($variables);
  }

}
