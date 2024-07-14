<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Template\Attribute;

final readonly class InputPreprocess {

  public function __invoke(array &$variables): void {
    $variables['input_props'] = [];
    $input_props = &$variables['input_props'];

    $component_attributes = new Attribute();

    if (isset($variables['attributes']['type']['data-drupal-selector'])) {
      $component_attributes->setAttribute(
        attribute: 'data-drupal-selector',
        value: $variables['attributes']['type']['data-drupal-selector'],
      );
    }

    $classes_to_ignore = ['form-text', 'required'];

    foreach ($variables['attributes']['class'] ?? [] as $class) {
      if (\in_array($class, $classes_to_ignore, TRUE)) {
        continue;
      }

      $component_attributes->addClass($class);
    }

    $input_props['attributes'] = $component_attributes;
    $input_props['required'] = $variables['element']['#required'];

    foreach ($variables['attributes'] as $attribute => $value) {
      match ($attribute) {
        default => NULL,
        'autocapitalize' => $input_props['autocapitalize'] = $value,
        'spellcheck' => $input_props['spellcheck'] = $value === 'true',
        'autofocus' => $input_props['autofocus'] = $value,
        'type' => $input_props['type'] = $value,
        'name' => $input_props['name'] = $value,
        'size' => $input_props['size'] = $value,
        'maxlength' => $input_props['maxlength'] = $value,
        'value' => $input_props['value'] = $value,
        'id' => $input_props['id'] = $value,
      };
    }
  }

}
