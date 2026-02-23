<?php

declare(strict_types=1);

namespace Drupal\app_main\StaticPage\Services\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\app_contract\LanguageAwareStore\LanguageAwareStoreForm;
use Drupal\app_main\StaticPage\Services\Repository\ServicesSettings;

final class ServicesSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'app_main_services_settings';
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
      ->getSettings()
      // @phpstan-ignore-next-line argument.type
      ->setDescription($form_state->getValue(['description', 'value']))
      // @phpstan-ignore-next-line argument.type
      ->setHourlyRate($form_state->getValue(['hourly_rate']));

    parent::submitForm($form, $form_state);
  }

  #[\Override]
  protected function getSettings(): ServicesSettings {
    $settings = $this->getContainer()->get(ServicesSettings::class);
    \assert($settings instanceof ServicesSettings);

    return $settings;
  }

}
