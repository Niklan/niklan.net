<?php

declare(strict_types=1);

namespace Drupal\app_image\Twig;

use Drupal\app_image\DynamicImageStyle\DynamicImageStyle;
use Drupal\app_image\DynamicImageStyle\DynamicImageStyleBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\image\ImageStyleInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

#[AutoconfigureTag('twig.extension')]
final class DynamicImageStyleExtension extends AbstractExtension {

  public function __construct(
    private readonly DynamicImageStyle $dynamicImageStyle,
    private readonly ImageFactory $imageFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function getFilters(): array {
    return [
      new TwigFilter('image_scale_crop', $this->imageScaleCrop(...)),
      new TwigFilter('image_scale', $this->imageScale(...)),
      new TwigFilter('image_convert', $this->imageConvert(...)),
      new TwigFilter('image_set_canvas', $this->imageSetCanvas(...)),
      new TwigFilter('dynamic_image_style', $this->dynamicImageStyle(...)),
      new TwigFilter('dynamic_image_dimensions', $this->dynamicImageDimensions(...)),
      new TwigFilter('image_dimensions', $this->imageDimensions(...)),
    ];
  }

  public function imageScaleCrop(string|DynamicImageStyleBuilder $input, int $width, int $height): DynamicImageStyleBuilder {
    return $this->ensureBuilder($input)->effect('image_scale_and_crop', ['width' => $width, 'height' => $height]);
  }

  public function imageScale(string|DynamicImageStyleBuilder $input, ?int $width = NULL, ?int $height = NULL): DynamicImageStyleBuilder {
    $data = \array_filter(['width' => $width, 'height' => $height], static fn ($v): bool => $v !== NULL);
    return $this->ensureBuilder($input)->effect('image_scale', $data);
  }

  public function imageConvert(string|DynamicImageStyleBuilder $input, string $extension): DynamicImageStyleBuilder {
    return $this->ensureBuilder($input)->effect('image_convert', ['extension' => $extension]);
  }

  public function imageSetCanvas(string|DynamicImageStyleBuilder $input, array $data): DynamicImageStyleBuilder {
    $canvas_defaults = [
      'canvas_size' => 'exact',
      'canvas_color' => '',
      'exact' => [
        'width' => 0,
        'height' => 0,
        'placement' => 'center-center',
        'x_offset' => 0,
        'y_offset' => 0,
      ],
    ];
    $data = \array_replace_recursive($canvas_defaults, $data);
    return $this->ensureBuilder($input)->effect('image_effects_set_canvas', $data);
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function dynamicImageStyle(string|DynamicImageStyleBuilder $input, array $effects = []): DynamicImageStyleBuilder {
    $builder = $this->ensureBuilder($input);
    foreach ($effects as [$id, $data]) {
      $builder = $builder->effect($id, $data);
    }
    return $builder;
  }

  /**
   * @return array{width: ?int, height: ?int}
   */
  public function dynamicImageDimensions(DynamicImageStyleBuilder $builder): array {
    $uri = $builder->getUri();
    if ($uri === NULL) {
      return ['width' => NULL, 'height' => NULL];
    }
    $image = $this->imageFactory->get($uri);
    if (!$image->isValid()) {
      return ['width' => NULL, 'height' => NULL];
    }
    $dimensions = ['width' => $image->getWidth(), 'height' => $image->getHeight()];
    $image_style = $this->dynamicImageStyle->createImageStyle($builder->getEffects());
    $image_style->transformDimensions($dimensions, $uri);
    // @phpstan-ignore return.type (transformDimensions passes by reference, losing shape info)
    return $dimensions;
  }

  /**
   * @return array{width: ?int, height: ?int}
   */
  public function imageDimensions(string $uri, ?string $style_name = NULL): array {
    $dimensions = ['width' => NULL, 'height' => NULL];
    $image = $this->imageFactory->get($uri);
    if (!$image->isValid()) {
      return $dimensions;
    }
    $dimensions['width'] = $image->getWidth();
    $dimensions['height'] = $image->getHeight();
    if ($style_name) {
      $style = $this->entityTypeManager->getStorage('image_style')->load($style_name);
      if ($style instanceof ImageStyleInterface) {
        $style->transformDimensions($dimensions, $uri);
      }
    }
    // @phpstan-ignore return.type (transformDimensions passes by reference, losing shape info)
    return $dimensions;
  }

  private function ensureBuilder(string|DynamicImageStyleBuilder $input): DynamicImageStyleBuilder {
    if ($input instanceof DynamicImageStyleBuilder) {
      return $input;
    }
    return new DynamicImageStyleBuilder($this->dynamicImageStyle, uri: $input);
  }

}
