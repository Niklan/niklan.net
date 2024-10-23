<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Contract\Repository\AboutSettings as AboutSettingsInterface;
use Drupal\niklan\Repository\AboutSettings as AboutSettingsRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class AboutSettings implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  public function __construct(
    private AboutSettingsInterface $settings,
    private MessengerInterface $messenger,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(AboutSettingsRepository::class),
      $container->get(MessengerInterface::class),
    );
  }

  #[\Override]
  public function getFormId(): string {
    return 'niklan_about_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;

    $this->buildPhotoSettings($form, $form_state);
    $this->buildContentSettings($form, $form_state);

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
      ->setPhotoMediaId($form_state->getValue(['photo', 'media_id']))
      ->setTitle($form_state->getValue(['content', 'title']))
      ->setSubtitle($form_state->getValue(['content', 'subtitle', 'value']))
      ->setSummary($form_state->getValue(['content', 'summary', 'value']))
      ->setDescription($form_state->getValue(['content', 'description', 'value']));

    $this
      ->messenger
      ->addStatus(new TranslatableMarkup('Settings successfully saved.'));
  }

  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not needed.
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
      '#default_value' => $this->settings->getTitle(),
      '#required' => TRUE,
    ];

    $form['content']['subtitle'] = [
      '#type' => 'text_format',
      '#base_type' => 'textfield',
      '#title' => new TranslatableMarkup('Subtitle'),
      '#description' => new TranslatableMarkup('The subtitle of the about page.'),
      '#default_value' => $this->settings->getSubtitle(),
      '#allowed_formats' => [AboutSettingsInterface::TEXT_FORMAT],
      '#required' => TRUE,
    ];

    $form['content']['summary'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Summary'),
      '#description' => new TranslatableMarkup('The summary of the about page.'),
      '#default_value' => $this->settings->getSummary(),
      '#allowed_formats' => [AboutSettingsInterface::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['content']['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Description'),
      '#description' => new TranslatableMarkup('The description of the about page.'),
      '#default_value' => $this->settings->getDescription(),
      '#allowed_formats' => [AboutSettingsInterface::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];
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
