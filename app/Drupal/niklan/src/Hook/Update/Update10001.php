<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Update;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a checksum field for file entity.
 *
 * @see \niklan_update_10001()
 */
final class Update10001 implements ContainerInjectionInterface {

  /**
   * Constructs a new Update10001 instance.
   *
   * @param \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface $entityDefinitionUpdateManager
   *   The entity definitions update manager.
   */
  public function __construct(
    protected EntityDefinitionUpdateManagerInterface $entityDefinitionUpdateManager,
  ) {}

  /**
   * Implements hook_update_N().
   */
  public function __invoke(array &$sandbox): string {
    $storage_definition = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('The file checksum'))
      ->setDescription(new TranslatableMarkup('The file MD5 checksum.'))
      ->setSetting('max_length', 255);

    $this->entityDefinitionUpdateManager->installFieldStorageDefinition(
      'niklan_checksum',
      'file',
      'niklan',
      $storage_definition,
    );

    return 'File checksum field has been installed.';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity.definition_update_manager'),
    );
  }

}
