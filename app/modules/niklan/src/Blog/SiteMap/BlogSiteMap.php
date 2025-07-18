<?php

declare(strict_types=1);

namespace Drupal\niklan\Blog\SiteMap;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\niklan\SiteMap\Structure\Category;
use Drupal\niklan\SiteMap\Structure\Section;
use Drupal\niklan\SiteMap\Structure\SiteMap;
use Drupal\niklan\SiteMap\Structure\SiteMapBuilderInterface;

final readonly class BlogSiteMap implements SiteMapBuilderInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function build(): SiteMap {
    $sitemap = new SiteMap();
    $category = new Category(new TranslatableMarkup('Blog'));
    $section = new Section(new TranslatableMarkup('Articles'));

    foreach ($this->articles() as $article) {
      \assert($article instanceof BlogEntry);
      $section->add($article->toLink());
    }

    $category->add($section);
    $sitemap->add($category);
    $sitemap->addCacheTags(['node_list:blog_entry']);

    return $sitemap;
  }

  private function articles(): \Generator {
    $storage = $this->entityTypeManager->getStorage('node');
    $ids = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('type', 'blog_entry')
      ->sort('nid', 'DESC')
      ->execute();

    yield from $storage->loadMultiple($ids);
  }

}
