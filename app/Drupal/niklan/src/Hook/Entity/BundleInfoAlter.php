<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Entity\File\File;
use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Entity\Node\Portfolio;

/**
 * Alters entity bundle information.
 *
 * @see niklan_entity_bundle_info_alter()
 */
final class BundleInfoAlter {

  /**
   * Alters bundle classes for entities.
   *
   * @param array $bundles
   *   The array with entity bundle formation.
   */
  protected function alterBundleClasses(array &$bundles): void {
    $bundle_classes_map = [
      'node' => [
        'blog_entry' => [
          'class' => BlogEntry::class,
          'label' => new TranslatableMarkup('Blog article'),
        ],
        'portfolio' => [
          'class' => Portfolio::class,
          'label' => new TranslatableMarkup('Portfolio project'),
        ],
      ],
      'file' => [
        'file' => [
          'class' => File::class,
          'label' => new TranslatableMarkup('File'),
        ],
      ],
    ];

    $bundles = NestedArray::mergeDeep($bundles, $bundle_classes_map);
  }

  /**
   * Implements hook_entity_bundle_info_alter().
   */
  public function __invoke(array &$bundles): void {
    $this->alterBundleClasses($bundles);
  }

}
