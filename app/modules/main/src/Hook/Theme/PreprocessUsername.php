<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('preprocess_username')]
final readonly class PreprocessUsername {

  public function __invoke(array &$variables): void {
    unset($variables['extra']);
    unset($variables['link_path']);
    unset($variables['attributes']['rel']);
    $variables['attributes']['class'][] = 'username';
  }

}
