<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Template\Attribute;

final readonly class PreprocessInput {

  private function preprocessCheckbox(array &$variables): void {
    $element = &$variables['element'];
    $variables['input_props'] = [
      'input_attributes' => new Attribute($variables['attributes']),
      'checked' => $element['#checked'],
      'label' => $element['#title'],
      'required' => $element['#required'],
    ];
    \array_filter($variables['input_props']);
  }

  public function __invoke(array &$variables): void {
    $classes_to_remove = ['form-text', 'required', 'form-checkbox'];
    foreach ($variables['attributes']['class'] ?? [] as $index => $class) {
      if (!\in_array($class, $classes_to_remove, TRUE)) {
        continue;
      }

      unset($variables['attributes']['class'][$index]);
    }

    match ($variables['element']['#type']) {
      default => NULL,
      'checkbox' => $this->preprocessCheckbox($variables),
    };
  }

}
