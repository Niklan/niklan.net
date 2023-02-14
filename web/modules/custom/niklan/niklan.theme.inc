<?php declare(strict_types = 1);

/**
 * @file
 * Contains preprocess and process functions for custom templates.
 */

use Drupal\media\MediaInterface;
use Drupal\responsive_image\ResponsiveImageStyleInterface;

/**
 * Prepares variables for about page templates.
 *
 * Default template: niklan-about-page.html.twig.
 *
 * @param array $variables
 *   An associative array with template variables.
 */
function template_preprocess_niklan_about_page(array &$variables): void {
  $about_settings = \Drupal::service('niklan.repository.about_settings');
  if (!$about_settings->getPhotoMediaId()) {
    return;
  }

  /** @var \Drupal\media\MediaStorage $media_storage */
  $media_storage = Drupal::entityTypeManager()->getStorage('media');
  $responsive_image_style_storage = Drupal::entityTypeManager()
    ->getStorage('responsive_image_style');
  $file_storage = Drupal::entityTypeManager()->getStorage('file');

  /** @var \Drupal\media\MediaInterface $photo_media */
  $photo_media = $media_storage->load($about_settings->getPhotoMediaId());
  $responsive_image_style = $responsive_image_style_storage->load(
    $about_settings->getPhotoResponsiveImageStyleId(),
  );
  $variables['photo'] = NULL;
  if (!$photo_media instanceof MediaInterface || !$responsive_image_style instanceof ResponsiveImageStyleInterface) {
    return;
  }

  $file_id = $photo_media->getSource()->getSourceFieldValue($photo_media);
  $file = $file_storage->load($file_id);
  $variables['photo'] = [
    '#type' => 'responsive_image',
    '#responsive_image_style_id' => $responsive_image_style->id(),
    '#uri' => $file->getFileUri(),
  ];
}
