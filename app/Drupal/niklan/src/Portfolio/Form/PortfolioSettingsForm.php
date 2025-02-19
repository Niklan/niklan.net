<?php

declare(strict_types=1);

namespace Drupal\niklan\Portfolio\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\LanguageAwareStore\Form\LanguageAwareStoreForm;
use Drupal\niklan\Portfolio\Repository\PortfolioSettings;

final class PortfolioSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'niklan_portfolio_settings';
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
