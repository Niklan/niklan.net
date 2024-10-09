<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

final readonly class Preprocess {

  private function passComponentData(array &$variables): void {
    $data = $variables['element'] ?? $variables;

    if (!\array_key_exists('#component_data', $data)) {
      return;
    }

    $variables += $data['#component_data'];
  }

  public function __invoke(array &$variables, string $hook): void {
    $this->passComponentData($variables);
  }

}
