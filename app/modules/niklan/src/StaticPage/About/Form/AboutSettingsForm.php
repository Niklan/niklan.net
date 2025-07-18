<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\About\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\LanguageAwareStore\Form\LanguageAwareStoreForm;
use Drupal\niklan\StaticPage\About\Repository\AboutSettings;

final class AboutSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'niklan_about_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $this->buildPhotoSettings($form, $form_state);
    $this->buildContentSettings($form, $form_state);

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /**
     * @var array{
     *   media_id: ?string,
     *   title: string,
     *   subtitle: array{value: string},
     *   summary: array{value: string},
     *   description: array{value: string},
     * } $values
     */
    $values = $form_state->getValues();

    $settings = $this->getSettings();
    $settings->setPhotoMediaId($values['media_id']);
    $settings->setTitle($values['title']);
    $settings->setSubtitle($values['subtitle']['value']);
    $settings->setSummary($values['summary']['value']);
    $settings->setDescription($values['description']['value']);

    parent::submitForm($form, $form_state);
  }

  public function buildContentSettings(array &$form, FormStateInterface $form_state): void {
    $form['content'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Content'),
    ];

    $form['content']['title'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Title'),
      '#description' => new TranslatableMarkup('The title of the about page.'),
      '#default_value' => $this->getSettings()->getTitle(),
      '#required' => TRUE,
    ];

    $form['content']['subtitle'] = [
      '#type' => 'text_format',
      '#base_type' => 'textfield',
      '#title' => new TranslatableMarkup('Subtitle'),
      '#description' => new TranslatableMarkup('The subtitle of the about page.'),
      '#default_value' => $this->getSettings()->getSubtitle(),
      '#allowed_formats' => [AboutSettings::TEXT_FORMAT],
      '#required' => TRUE,
    ];

    $form['content']['summary'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Summary'),
      '#description' => new TranslatableMarkup('The summary of the about page.'),
      '#default_value' => $this->getSettings()->getSummary(),
      '#allowed_formats' => [AboutSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['content']['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Description'),
      '#description' => new TranslatableMarkup('The description of the about page.'),
      '#default_value' => $this->getSettings()->getDescription(),
      '#allowed_formats' => [AboutSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];
  }

  #[\Override]
  protected function getSettings(): AboutSettings {
    $settings = $this->getContainer()->get(AboutSettings::class);
    \assert($settings instanceof AboutSettings);

    return $settings;
  }

  private function buildPhotoSettings(array &$form, FormStateInterface $form_state): void {
    $form['photo'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Photo'),
    ];

    $form['photo']['media_id'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['image'],
      '#title' => new TranslatableMarkup('Photo'),
      '#description' => new TranslatableMarkup('Media entity that contains a photo.'),
      '#default_value' => $this->getSettings()->getPhotoMediaId(),
    ];
  }

}
