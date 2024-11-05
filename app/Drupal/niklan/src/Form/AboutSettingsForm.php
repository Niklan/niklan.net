<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Repository\KeyValue\AboutSettings;
use Drupal\niklan\Repository\KeyValue\LanguageAwareSettingsStore;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class AboutSettingsForm extends SettingsForm {

  public function __construct(
    protected AboutSettings $settings,
    protected MessengerInterface $messenger,
    protected CacheTagsInvalidatorInterface $cacheTagsInvalidator,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(AboutSettings::class),
      $container->get(MessengerInterface::class),
      $container->get(CacheTagsInvalidatorInterface::class),
    );
  }

  #[\Override]
  public function getFormId(): string {
    return 'niklan_about_settings';
  }

  #[\Override]
  public function doBuildForm(array &$form, FormStateInterface $form_state): void {
    $this->buildPhotoSettings($form, $form_state);
    $this->buildContentSettings($form, $form_state);
  }

  #[\Override]
  public function doSubmitForm(array &$form, FormStateInterface $form_state): void {
    $this
      ->getSettings()
      ->setPhotoMediaId($form_state->getValue('media_id'))
      ->setTitle($form_state->getValue('title'))
      ->setSubtitle($form_state->getValue(['subtitle', 'value']))
      ->setSummary($form_state->getValue(['summary', 'value']))
      ->setDescription($form_state->getValue(['description', 'value']));
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

  protected function getMessenger(): MessengerInterface {
    return $this->messenger;
  }

  protected function getCacheTagsInvalidator(): CacheTagsInvalidatorInterface {
    return $this->cacheTagsInvalidator;
  }

  protected function getSettings(): LanguageAwareSettingsStore {
    return $this->settings;
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
      '#default_value' => $this->settings->getPhotoMediaId(),
    ];
  }

}
