<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Content\Blog\Entity\BlogEntry;
use Drupal\niklan\Content\File\Entity\File;
use Drupal\niklan\Content\Portfolio\Entity\Portfolio;

final class BundleInfoAlter {

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

  public function __invoke(array &$bundles): void {
    $this->alterBundleClasses($bundles);
  }

}
