<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

final readonly class Preprocess {

  /**
   * Passes #component_data into template variables.
   *
   * Bridges Drupal render API and SDC props/slots.
   *
   * @see \Drupal\laszlo\Hook\Form\FormCommentFormAlter
   */
  private function passComponentData(array &$variables): void {
    // 'element' can be a scalar (e.g. int in pagers).
    $data = \is_array($variables['element'] ?? NULL)
        ? $variables['element']
        : $variables;

    if (!\array_key_exists('#component_data', $data)) {
      return;
    }

    $variables += $data['#component_data'];
  }

  public function __invoke(array &$variables, string $hook): void {
    $this->passComponentData($variables);
  }

}
