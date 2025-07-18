<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Twig\Extension;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\image\ImageStyleInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ImageDimensions extends AbstractExtension {

  public function __construct(
    private ImageFactory $imageFactory,
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function getFilters(): array {
    return [
      new TwigFilter('image_dimensions', $this->getImageDimensions(...)),
    ];
  }

  public function getImageDimensions(string $uri, ?string $style_name = NULL): array {
    $dimensions = [
      'width' => NULL,
      'height' => NULL,
    ];

    $image = $this->imageFactory->get($uri);

    if (!$image->isValid()) {
      return $dimensions;
    }

    $dimensions['width'] = $image->getWidth();
    $dimensions['height'] = $image->getHeight();

    if ($style_name) {
      $this->processImageStyle($uri, $style_name, $dimensions);
    }

    return $dimensions;
  }

  private function processImageStyle(string $uri, string $style_name, array &$dimensions): void {
    $style = $this
      ->entityTypeManager
      ->getStorage('image_style')
      ->load($style_name);

    if (!$style instanceof ImageStyleInterface) {
      return;
    }

    $style->transformDimensions($dimensions, $uri);
  }

}
