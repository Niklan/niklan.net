<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\FileStorageInterface;
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
   * The file storage.
   */
  protected ?FileStorageInterface $fileStorage;

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
    $target_type = $field_definition->getFieldStorageDefinition()
      ->getSetting('target_type');
    return $target_type == 'media';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $item) {
      /** @var \Drupal\media\MediaInterface $media */
      $media = $item->entity;
      $file_id = $media->getSource()->getSourceFieldValue($media);
      /** @var \Drupal\file\FileInterface $file */
      $file = $this->getFileStorage()->load($file_id);

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

  /**
   * Gets file storage.
   *
   * @return \Drupal\file\FileStorageInterface
   *   The file storage.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getFileStorage(): FileStorageInterface {
    if (!isset($this->fileStorage)) {
      $this->fileStorage = $this->entityTypeManager->getStorage('file');
    }

    return $this->fileStorage;
  }

}
