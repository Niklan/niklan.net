<?php

declare(strict_types=1);

namespace Drupal\app_file\Hook\Entity;

use Drupal\app_file\File\FileBundle;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslationInterface;

#[Hook('entity_bundle_info_alter')]
final class BundleInfoAlter {

  public function __construct(
    private readonly TranslationInterface $stringTranslation,
  ) {}

  public function __invoke(array &$bundles): void {
    $this->alterBundleClasses($bundles);
  }

  protected function alterBundleClasses(array &$bundles): void {
    $bundle_classes_map = [
      'file' => [
        'file' => [
          'class' => FileBundle::class,
          'label' => $this->stringTranslation->translate('File'),
        ],
      ],
    ];

    $bundles = NestedArray::mergeDeep($bundles, $bundle_classes_map);
  }

}
