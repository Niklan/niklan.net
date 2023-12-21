<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\media\MediaInterface;
use Drupal\responsive_image\Plugin\Field\FieldFormatter\ResponsiveImageFormatter;

/**
 * Plugin implementation of the media responsive thumbnail formatter.
 *
 * @FieldFormatter(
 *   id = "niklan_responsive_media_thumbnail",
 *   label = @Translation("Responsive thumbnail"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
final class MediaResponsiveThumbnailFormatter extends ResponsiveImageFormatter {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    // This formatter is only available for entity types that reference
    // media items.
    return $field_definition
      ->getFieldStorageDefinition()
      ->getSetting('target_type') === 'media';
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $link_types = [
      'content' => new TranslatableMarkup('Content'),
      'media' => new TranslatableMarkup('Media item'),
    ];
    $element['image_link']['#options'] = $link_types;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();

    $link_types = [
      'content' => new TranslatableMarkup('Linked to content'),
      'media' => new TranslatableMarkup('Linked to media item'),
    ];
    // Display this setting only if image is linked.
    $image_link_setting = $this->getSetting('image_link');

    if (isset($link_types[$image_link_setting])) {
      $summary[] = $link_types[$image_link_setting];
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    \assert($items instanceof EntityReferenceFieldItemListInterface);

    $elements = [];
    $media_items = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (!$media_items) {
      return $elements;
    }

    $responsive_image_style = $this->getSetting('responsive_image_style');

    foreach ($media_items as $delta => $media) {
      \assert($media instanceof MediaInterface);

      $cache_tags = [];
      $cache_tags = Cache::mergeTags($cache_tags, $media->getCacheTags());
      $elements[$delta] = [
        '#theme' => 'responsive_image_formatter',
        '#responsive_image_style_id' => $responsive_image_style ?? '',
        '#item' => $media->get('thumbnail')->first(),
        '#item_attributes' => [],
        '#url' => $this->getMediaThumbnailUrl($media, $items->getEntity()),
        '#cache' => [
          'tags' => $cache_tags,
        ],
      ];
    }

    // Add cacheability of the image style setting.
    if ($this->getSetting('image_link')) {
      $image_style = $this
        ->responsiveImageStyleStorage
        ->load($responsive_image_style);

      if ($image_style) {
        $cache_a = CacheableMetadata::createFromRenderArray($elements);
        $cache_b = CacheableMetadata::createFromObject($image_style);
        $cache_a->merge($cache_b)->applyTo($elements);
      }
    }

    return $elements;
  }

  /**
   * Get the URL for the media thumbnail.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media item.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that the field belongs to.
   *
   * @return \Drupal\Core\Url|null
   *   The URL object for the media item or null if we don't want to add
   *   a link.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function getMediaThumbnailUrl(MediaInterface $media, EntityInterface $entity): ?Url {
    $url = NULL;
    $image_link_setting = $this->getSetting('image_link');

    // Check if the formatter involves a link.
    if ($image_link_setting === 'content') {
      if (!$entity->isNew()) {
        $url = $entity->toUrl();
      }
    }
    elseif ($image_link_setting === 'media') {
      $url = $media->toUrl();
    }

    return $url;
  }

  /**
   * {@inheritdoc}
   *
   * This has to be overridden because FileFormatterBase expects $item to be
   * of type \Drupal\file\Plugin\Field\FieldType\FileItem and calls
   * isDisplayed() which is not in FieldItemInterface.
   */
  protected function needsEntityLoad(EntityReferenceItem $item): bool {
    return !$item->hasNewEntity();
  }

}
