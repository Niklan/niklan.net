<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\File\Entity\FileBundle;
use Drupal\niklan\Node\Entity\ArticleBundle;
use Drupal\niklan\Node\Entity\PortfolioBundle;
use Drupal\niklan\Tag\Entity\TagBundle;

#[Hook('entity_bundle_info_alter')]
final class BundleInfoAlter {

  protected function alterBundleClasses(array &$bundles): void {
    $bundle_classes_map = [
      'node' => [
        'blog_entry' => [
          'class' => ArticleBundle::class,
          'label' => new TranslatableMarkup('Blog article'),
        ],
        'portfolio' => [
          'class' => PortfolioBundle::class,
          'label' => new TranslatableMarkup('Portfolio project'),
        ],
      ],
      'file' => [
        'file' => [
          'class' => FileBundle::class,
          'label' => new TranslatableMarkup('File'),
        ],
      ],
      'taxonomy_term' => [
        'tags' => [
          'class' => TagBundle::class,
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
