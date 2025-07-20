<?php

declare(strict_types=1);

namespace Drupal\niklan\Utils;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;

final readonly class AjaxFormHelper {

  public static function refresh(array $form, FormStateInterface $form_state): AjaxResponse {
    $element = self::resolveTargetElement($form, $form_state);
    $response = new AjaxResponse();

    self::addFormReplaceCommand($form, $response);
    self::addStatusMessagesCommand($element, $response);

    return $response;
  }

  private static function resolveTargetElement(array $form, FormStateInterface $form_state): array {
    $triggering_element = $form_state->getTriggeringElement();

    if (isset($triggering_element['#ajax']['element'])) {
      $element = NestedArray::getValue($form, $triggering_element['#ajax']['element']);
      return \is_array($element) ? $element : $form;
    }

    return $form;
  }

  private static function addFormReplaceCommand(array $form, AjaxResponse $response): void {
    $form_selector = self::extractDataSelector($form);

    if (!$form_selector) {
      return;
    }

    $response->addCommand(new ReplaceCommand(
    self::formatSelector($form_selector),
    $form,
    ));
  }

  private static function addStatusMessagesCommand(array $element, AjaxResponse $response): void {
    $element_selector = self::extractDataSelector($element);

    if (!$element_selector) {
      return;
    }

    $response->addCommand(new PrependCommand(
    self::formatSelector($element_selector),
    ['#type' => 'status_messages'],
    ));
  }

  private static function extractDataSelector(array $element): ?string {
    return $element['#attributes']['data-drupal-selector'] ?? NULL;
  }

  private static function formatSelector(string $selector): string {
    return \sprintf('[data-drupal-selector="%s"]', $selector);
  }

}
