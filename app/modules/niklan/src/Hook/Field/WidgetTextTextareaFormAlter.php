<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Field;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Hook('field_widget_text_textarea_form_alter')]
final readonly class WidgetTextTextareaFormAlter {

  public static function removeFormat(array $element): array {
    $is_admin = \Drupal::routeMatch()->getRouteObject()?->hasOption('_admin_route') ?? FALSE;

    if ($element['#type'] === 'text_format' && !$is_admin) {
      \hide($element['format']);
    }

    if ($element['#type'] === 'textarea') {
      $element['#description'] = new TranslatableMarkup(
        'Supports <a href="@common_mark_url" target="_blank">CommonMark</a> syntax.',
        ['@common_mark_url' => 'https://commonmark.org/help/'],
      );
    }

    return $element;
  }

  public function __invoke(array &$element, FormStateInterface $form_state, array $context): void {
    $items = $context['items'];
    \assert($items instanceof FieldItemListInterface);
    $field_name = $items->getFieldDefinition()->getName();

    if ($field_name !== 'comment_body') {
      return;
    }

    $element['#after_build'][] = [self::class, 'removeFormat'];
  }

}
