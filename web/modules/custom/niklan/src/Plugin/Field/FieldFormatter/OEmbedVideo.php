<?php

declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\media\Entity\MediaType;
use Drupal\media\Plugin\media\Source\OEmbedInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the optimized oEmbed video formatter.
 *
 * @FieldFormatter(
 *   id = "niklan_media_remote_video_optimized",
 *   label = @Translation("oEmbed video optimized responsive"),
 *   field_types = {
 *     "link",
 *     "string",
 *     "string_long",
 *   },
 * )
 */
final class OEmbedVideo extends FormatterBase {

  /**
   * The responsive image style storage.
   */
  protected EntityStorageInterface $responsiveImageStyleStorage;

  /**
   * The link generator.
   */
  protected LinkGeneratorInterface $linkGenerator;

  /**
   * The current user.
   */
  protected AccountInterface $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition,
    );

    $instance->responsiveImageStyleStorage = $container
      ->get('entity_type.manager')
      ->getStorage('responsive_image_style');
    $instance->linkGenerator = $container->get('link_generator');
    $instance->currentUser = $container->get('current_user');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    if ($field_definition->getTargetEntityTypeId() !== 'media') {
      return FALSE;
    }

    $media_type = $field_definition->getTargetBundle();
    if ($media_type = MediaType::load($media_type)) {
      $is_video = $media_type->getSource()->getPluginDefinition()['id'] === 'video';

      return $media_type->getSource() instanceof OEmbedInterface && $is_video;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'responsive_image_style' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $responsive_image_options = [];
    foreach ($this->responsiveImageStyleStorage->loadMultiple() as $machine_name => $responsive_image_style) {
      if ($responsive_image_style->hasImageStyleMappings()) {
        $responsive_image_options[$machine_name] = $responsive_image_style->label();
      }
    }

    $element['responsive_image_style'] = [
      '#title' => new TranslatableMarkup('Responsive image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('responsive_image_style') ?: NULL,
      '#required' => TRUE,
      '#options' => $responsive_image_options,
      '#description' => [
        '#markup' => $this->linkGenerator->generate(
          new TranslatableMarkup('Configure Responsive Image Styles'),
          new Url('entity.responsive_image_style.collection'),
        ),
        '#access' => $this->currentUser->hasPermission(
          'administer responsive image styles',
        ),
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $responsive_image_style = $this->responsiveImageStyleStorage->load(
      $this->getSetting('responsive_image_style'),
    );
    if ($responsive_image_style) {
      $summary[] = new TranslatableMarkup(
        'Responsive image style: @responsive_image_style',
        ['@responsive_image_style' => $responsive_image_style->label()],
      );
    }
    else {
      $summary[] = new TranslatableMarkup('Select a responsive image style.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'niklan_oembed_video',
        '#media' => $item->getEntity(),
        '#preview_responsive_image_style' => $this->getSetting(
          'responsive_image_style',
        ),
      ];
    }

    return $elements;
  }

}
