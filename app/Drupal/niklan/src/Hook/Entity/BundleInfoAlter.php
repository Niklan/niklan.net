<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\File\Entity\File;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\niklan\Node\Entity\Portfolio;
use Drupal\niklan\Tag\Entity\Tag;

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
      'taxonomy_term' => [
        'tags' => [
          'class' => Tag::class,
          'label' => new TranslatableMarkup('Tag'),
        ],
      ],
    ];

    $bundles = NestedArray::mergeDeep($bundles, $bundle_classes_map);
  }

  public function __invoke(array &$bundles): void {
    $this->alterBundleClasses($bundles);
  }

}
