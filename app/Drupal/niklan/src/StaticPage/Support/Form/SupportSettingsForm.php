<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Support\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\LanguageAwareStore\Form\LanguageAwareStoreForm;
use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;
use Drupal\niklan\StaticPage\Support\Repository\SupportSettings;

final class SupportSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'niklan_support_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Body'),
      '#description' => new TranslatableMarkup('The description of support page.'),
      '#default_value' => $this->getSettings()->getDescription(),
      '#allowed_formats' => [$this->getSettings()::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['donate_url'] = [
      '#type' => 'url',
      '#title' => new TranslatableMarkup('Donate URL'),
      '#description' => new TranslatableMarkup('The URL of the donate page.'),
      '#default_value' => $this->getSettings()->getDonateUrl(),
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
      ->setDonateUrl($form_state->getValue(['donate_url']));

    parent::submitForm($form, $form_state);
  }

  #[\Override]
  protected function loadSettings(): LanguageAwareSettingsStore {
    $settings = $this->getContainer()->get(SupportSettings::class);
    \assert($settings instanceof SupportSettings);

    return $settings;
  }

}
