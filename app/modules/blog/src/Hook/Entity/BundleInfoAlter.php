<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Entity;

use Drupal\app_blog\Node\ArticleBundle;
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
      'node' => [
        'blog_entry' => [
          'class' => ArticleBundle::class,
          'label' => $this->stringTranslation->translate('Blog article'),
        ],
      ],
    ];

    $bundles = NestedArray::mergeDeep($bundles, $bundle_classes_map);
  }

}
