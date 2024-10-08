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

  private function preprocessSubmit(array &$variables): void {
    $variables['variant'] = match ($variables['element']['#button_variant'] ?? 'contained') {
      default => 'contained',
      'text' => 'text',
      'outlined' => 'outlined',
    };

    $variables['color'] = match ($variables['element']['#button_type'] ?? 'primary') {
      default => 'primary',
      'secondary' => 'secondary',
      'custom' => 'custom',
      'danger' => 'danger',
    };
  }

  private function addDecoratorSupport(array &$variables): void {
    $variables['start_decorator'] = $variables['element']['#start_decorator'] ?? NULL;
    $variables['end_decorator'] = $variables['element']['#end_decorator'] ?? NULL;
  }

  public function __invoke(array &$variables): void {
    $classes_to_remove = ['form-text', 'required', 'form-checkbox'];
    foreach ($variables['attributes']['class'] ?? [] as $index => $class) {
      if (!\in_array($class, $classes_to_remove, TRUE)) {
        continue;
      }

      unset($variables['attributes']['class'][$index]);
    }

    $this->addDecoratorSupport($variables);

    match ($variables['element']['#type']) {
      default => NULL,
      'checkbox' => $this->preprocessCheckbox($variables),
      'submit' => $this->preprocessSubmit($variables),
    };
  }

}
