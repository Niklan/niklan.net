<?php

declare(strict_types=1);

namespace Drupal\niklan\Utils;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;

final readonly class AjaxFormHelper {

  /**
   * Provides a simple way to refresh AJAX form.
   *
   * This is basically copy of \Drupal\commerce\AjaxFormTrait::ajaxRefreshForm.
   *
   * Note that both the form and the element need to have an #id specified,
   * as a workaround to core bug #2897377.
   */
  public static function refresh(array $form, FormStateInterface $form_state): AjaxResponse {
    $triggering_element = $form_state->getTriggeringElement();
    $element = NULL;

    if (isset($triggering_element['#ajax']['element'])) {
      $element = NestedArray::getValue($form, $triggering_element['#ajax']['element']);
    }

    $element = \is_array($element) ? $element : $form;
    $response = new AjaxResponse();

    $form_selector = isset($form['#attributes']['data-drupal-selector']) && \is_string($form['#attributes']['data-drupal-selector'])
      ? $form['#attributes']['data-drupal-selector']
      : NULL;

    if ($form_selector) {
      $response->addCommand(new ReplaceCommand(
        selector: \sprintf('[data-drupal-selector="%s"]', $form_selector),
        content: $form,
      ));
    }

    $element_selector = isset($element['#attributes']['data-drupal-selector']) && \is_string($element['#attributes']['data-drupal-selector'])
      ? $element['#attributes']['data-drupal-selector']
      : NULL;

    if ($element_selector) {
      $response->addCommand(new PrependCommand(
        selector: \sprintf('[data-drupal-selector="%s"]', $element_selector),
        content: ['#type' => 'status_messages'],
      ));
    }

    return $response;
  }

}
