<?php

declare(strict_types=1);

namespace Drupal\app_tag\Hook\Entity;

use Drupal\app_tag\Entity\TagBundle;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Hook('entity_bundle_info_alter')]
final class BundleInfoAlter {

  public function __invoke(array &$bundles): void {
    $bundle_classes_map = [
      'taxonomy_term' => [
        'tags' => [
          'class' => TagBundle::class,
          'label' => new TranslatableMarkup('Tag'),
        ],
      ],
    ];

    $bundles = NestedArray::mergeDeep($bundles, $bundle_classes_map);
  }

}
