<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Contact\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\LanguageAwareStore\Form\LanguageAwareStoreForm;
use Drupal\niklan\StaticPage\Contact\Repository\ContactSettings;

final class ContactSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'niklan_contact_settings_form';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $form['email'] = [
      '#type' => 'email',
      '#title' => new TranslatableMarkup('Email'),
      '#default_value' => $this->getSettings()->getEmail(),
      '#required' => TRUE,
    ];

    $form['telegram'] = [
      '#type' => 'url',
      '#title' => new TranslatableMarkup('Telegram'),
      '#default_value' => $this->getSettings()->getTelegram(),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Description'),
      '#description' => new TranslatableMarkup('The description of the about page.'),
      '#default_value' => $this->getSettings()->getDescription(),
      '#allowed_formats' => [ContactSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $settings = $this->getSettings();

    \assert(\is_string($form_state->getValue('email')));
    $settings->setEmail($form_state->getValue('email'));
    \assert(\is_string($form_state->getValue('telegram')));
    $settings->setTelegram($form_state->getValue('telegram'));
    \assert(\is_string($form_state->getValue(['description', 'value'])));
    $settings->setDescription($form_state->getValue(['description', 'value']));

    parent::submitForm($form, $form_state);
  }

  protected function getSettings(): ContactSettings {
    return $this->getContainer()->get(ContactSettings::class);
  }

}
