<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Services\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\LanguageAwareStore\Form\LanguageAwareStoreForm;
use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;
use Drupal\niklan\StaticPage\Services\Repository\ServicesSettings;

final class ServicesSettingsForm extends LanguageAwareStoreForm {

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
      '#allowed_formats' => [ServicesSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['hourly_rate'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Hourly rate'),
      '#default_value' => $this->getSettings()->getHourlyRate(),
      '#required' => TRUE,
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
