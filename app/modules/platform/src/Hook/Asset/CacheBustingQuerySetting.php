<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Asset;

use Drupal\Core\Asset\AssetQueryStringInterface;
use Drupal\Core\Hook\Attribute\Hook;

#[Hook('js_settings_build')]
final readonly class CacheBustingQuerySetting {

  public function __construct(
    private AssetQueryStringInterface $queryString,
  ) {}

  public function __invoke(array &$settings): void {
    $settings['cacheQueryBustingString'] = $this->queryString->get();
  }

}
