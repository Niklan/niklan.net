<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;
use Drupal\media\MediaStorage;
use Drupal\niklan\Repository\AboutSettingsRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a setting form for about page contents.
 */
final class AboutSettingsForm extends FormBase {

  /**
   * The media storage.
   */
  protected MediaStorage $mediaStorage;

  /**
   * The responsive image style storage.
   */
  protected ConfigEntityStorageInterface $responsiveImageStyleStorage;

  /**
   * The settings storage.
   */
  protected AboutSettingsRepositoryInterface $settingsRepository;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $entity_type_manager = $container->get('entity_type.manager');

    $instance = new self();
    $instance->settingsRepository = $container->get('niklan.repository.about_settings');
    $instance->mediaStorage = $entity_type_manager->getStorage('media');
    $instance->responsiveImageStyleStorage = $entity_type_manager->getStorage('responsive_image_style');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'niklan_about_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;

    $form['photo'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Photo'),
    ];

    $default_image = NULL;
    if ($photo_media_id = $this->settingsRepository->getPhotoMediaId()) {
      $image_media = $this->mediaStorage->load($photo_media_id);
      if ($image_media instanceof MediaInterface) {
        $default_image = $image_media;
      }
    }
    $form['photo']['media_id'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['image'],
      ],
      '#title' => new TranslatableMarkup('Photo'),
      '#description' => new TranslatableMarkup('Media entity that contains a photo.'),
      '#default_value' => $default_image,
    ];

    $form['photo']['responsive_image_style'] = [
      '#type' => 'select',
      '#options' => $this->getResponsiveImageStyleOptions(),
      '#default_value' => $this->settingsRepository->getPhotoResponsiveImageStyleId(),
      '#title' => new TranslatableMarkup('Photo image style'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Gets options for responsive styles.
   *
   * @return array
   *   The array contains responsive image styles, where key is responsive image
   *   style id, and the value is label.
   */
  protected function getResponsiveImageStyleOptions(): array {
    $responsive_image_styles = $this->responsiveImageStyleStorage->loadMultiple();
    $responsive_image_style_options = [];
    foreach ($responsive_image_styles as $responsive_image_style) {
      $responsive_image_style_options[$responsive_image_style->id()] = $responsive_image_style->label();
    }
    return $responsive_image_style_options;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $responsive_image_style_id = $form_state->getValue([
      'photo',
      'responsive_image_style',
    ]);
    $this->settingsRepository
      ->setPhotoMediaId($form_state->getValue(['photo', 'media_id']))
      ->setPhotoResponsiveImageStyleId($responsive_image_style_id);
  }

}
