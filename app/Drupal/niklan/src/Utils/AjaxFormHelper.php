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
    // Element not specified or not found. Show messages on top of the form.
    if (!$element) {
      $element = $form;
    }
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('[data-drupal-selector="' . $form['#attributes']['data-drupal-selector'] . '"]', $form));
    $response->addCommand(new PrependCommand('[data-drupal-selector="' . $element['#attributes']['data-drupal-selector'] . '"]', ['#type' => 'status_messages']));

    return $response;
  }

}
