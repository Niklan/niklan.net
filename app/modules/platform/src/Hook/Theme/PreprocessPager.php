<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Theme;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

/**
 * Forces pager URLs through the outbound path processor.
 *
 * @ingroup seo_pager
 */
#[Hook('preprocess_pager')]
final readonly class PreprocessPager {

  /**
   * @param array{pager: array{'#route_name'?: string, '#fragment'?: string}, items?: array<mixed>} $variables
   */
  public function __invoke(array &$variables): void {
    // Only '<none>' pagers bypass path processors (the '_no_path' route
    // option). Other routes are already processed by the URL generator, so
    // handling them again would shift the page number twice.
    if (($variables['pager']['#route_name'] ?? '<none>') !== '<none>' || !isset($variables['items'])) {
      return;
    }

    $fragment = $variables['pager']['#fragment'] ?? NULL;
    \array_walk_recursive($variables['items'], function (mixed &$value, string|int $key) use ($fragment): void {
      $this->processHref($value, $key, $fragment);
    });
  }

  private function processHref(mixed &$value, string|int $key, ?string $fragment): void {
    if ($key !== 'href' || !\is_string($value)) {
      return;
    }

    $options = ['query' => UrlHelper::parse($value)['query']];
    if ($fragment !== NULL) {
      $options['fragment'] = $fragment;
    }

    $value = Url::fromRoute('<current>', [], $options)->toString();
  }

}
