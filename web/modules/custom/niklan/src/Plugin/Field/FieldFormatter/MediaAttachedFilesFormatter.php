<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'With icon' formatter.
 *
 * @FieldFormatter(
 *   id = "niklan_attached_files",
 *   label = @Translation("Attached files"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
final class MediaAttachedFilesFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

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

    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    $target_type = $field_definition
      ->getFieldStorageDefinition()
      ->getSetting('target_type');

    return $target_type === 'media';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $item) {
      \assert($item instanceof EntityReferenceItem);

      $media = $item->get('entity')->getValue();
      \assert($media instanceof MediaInterface);

      $file_id = $media->getSource()->getSourceFieldValue($media);
      $file = $this->entityTypeManager->getStorage('file')->load($file_id);
      \assert($file instanceof FileInterface);

      $element[] = [
        '#theme' => 'niklan_media_attached_file',
        '#uri' => $file->getFileUri(),
        '#filename' => $file->getFilename(),
        '#filesize' => \format_size($file->getSize()),
        '#label' => $media->label(),
      ];
    }

    return $element;
  }

}
