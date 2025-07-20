<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('preprocess_navigation')]
final readonly class PreprocessNavigation {

  public function __invoke(array &$variables): void {
    $variables['#attached']['library'][] = 'niklan/niklan.navigation';
  }

}
