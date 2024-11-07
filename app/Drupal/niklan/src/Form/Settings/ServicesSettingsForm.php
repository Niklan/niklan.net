<?php

declare(strict_types=1);

namespace Drupal\niklan\Form\Settings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Repository\KeyValue\LanguageAwareSettingsStore;
use Drupal\niklan\Repository\KeyValue\ServicesSettings;

final class ServicesSettingsForm extends SettingsForm {

  #[\Override]
  public function getFormId(): string {
    return 'niklan_services_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Body'),
      '#description' => new TranslatableMarkup('The description of service page.'),
      '#default_value' => $this->getSettings()->getDescription(),
      '#allowed_formats' => [$this->getSettings()::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['hourly_rate'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Hourly rate'),
      '#default_value' => $this->getSettings()->getHourlyRate(),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this
      ->settings
      ->setDescription($form_state->getValue(['description', 'value']))
      ->setHourlyRate($form_state->getValue(['hourly_rate']));

    parent::submitForm($form, $form_state);
  }

  #[\Override]
  protected function loadSettings(): LanguageAwareSettingsStore {
    $settings = $this->getContainer()->get(ServicesSettings::class);
    \assert($settings instanceof ServicesSettings);

    return $settings;
  }

}
