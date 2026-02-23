<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Hook\Entity;

use Drupal\app_portfolio\Node\PortfolioBundle;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Hook('entity_bundle_info_alter')]
final class BundleInfoAlter {

  public function __invoke(array &$bundles): void {
    $bundle_classes_map = [
      'node' => [
        'portfolio' => [
          'class' => PortfolioBundle::class,
          'label' => new TranslatableMarkup('Portfolio project'),
        ],
      ],
    ];

    $bundles = NestedArray::mergeDeep($bundles, $bundle_classes_map);
  }

}
