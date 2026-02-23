<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Form;

use Drupal\app_contract\LanguageAwareStore\LanguageAwareStoreForm;
use Drupal\app_portfolio\Repository\PortfolioSettings;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

final class PortfolioSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'app_portfolio_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Body'),
      '#description' => new TranslatableMarkup('The description of portfolio page.'),
      '#default_value' => $this->getSettings()->getDescription(),
      '#allowed_formats' => [PortfolioSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    \assert(\is_string($form_state->getValue(['description', 'value'])));
    $this->getSettings()->setDescription($form_state->getValue(['description', 'value']));

    parent::submitForm($form, $form_state);
  }

  #[\Override]
  protected function getSettings(): PortfolioSettings {
    $settings = $this->getContainer()->get(PortfolioSettings::class);
    \assert($settings instanceof PortfolioSettings);

    return $settings;
  }

}
