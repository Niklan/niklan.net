<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\media\Entity\MediaType;
use Drupal\media\MediaTypeInterface;
use Drupal\media\Plugin\media\Source\OEmbedInterface;
use Drupal\responsive_image\ResponsiveImageStyleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @deprecated Remove it.
 */
#[FieldFormatter(
  id: 'niklan_media_remote_video_optimized',
  label: new TranslatableMarkup('oEmbed video optimized responsive'),
  field_types: ['string'],
)]
final class OEmbedVideo extends FormatterBase {

  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LinkGeneratorInterface $linkGenerator,
    private readonly AccountInterface $currentUser,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get(EntityTypeManagerInterface::class),
      $container->get(LinkGeneratorInterface::class),
      $container->get(AccountInterface::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $storage = $this->entityTypeManager->getStorage('responsive_image_style');
    $responsive_image_options = [];

    foreach ($storage->loadMultiple() as $machine_name => $responsive_image_style) {
      \assert($responsive_image_style instanceof ResponsiveImageStyleInterface);

      if (!$responsive_image_style->hasImageStyleMappings()) {
        continue;
      }

      $responsive_image_options[$machine_name] = $responsive_image_style->label();
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

  #[\Override]
  public function settingsSummary(): array {
    $summary = [];

    $storage = $this->entityTypeManager->getStorage('responsive_image_style');
    $responsive_image_style = $storage
      ->load($this->getSetting('responsive_image_style'));

    if ($responsive_image_style) {
      $summary[] = (string) new TranslatableMarkup(
        // phpcs:disable Drupal.Semantics.FunctionT.NotLiteralString
        string: 'Responsive image style: @responsive_image_style',
        arguments: ['@responsive_image_style' => $responsive_image_style->label()],
      );
    }
    else {
      $summary[] = (string) new TranslatableMarkup('Select a responsive image style.');
    }

    return $summary;
  }

  #[\Override]
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

  #[\Override]
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    if ($field_definition->getTargetEntityTypeId() !== 'media') {
      return FALSE;
    }

    if (!$field_definition->getTargetBundle()) {
      return FALSE;
    }

    $media_type = MediaType::load($field_definition->getTargetBundle());

    if (!$media_type instanceof MediaTypeInterface) {
      return FALSE;
    }

    // @phpstan-ignore-next-line
    $is_video = $media_type->getSource()->getPluginDefinition()['id'] === 'video';
    $is_oembed = $media_type->getSource() instanceof OEmbedInterface;

    return $is_oembed && $is_video;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'responsive_image_style' => '',
    ] + parent::defaultSettings();
  }

}
