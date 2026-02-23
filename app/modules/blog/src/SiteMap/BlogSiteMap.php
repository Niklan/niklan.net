<?php

declare(strict_types=1);

namespace Drupal\app_blog\SiteMap;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\app_contract\Contract\Node\Article;
use Drupal\app_contract\Contract\SiteMap\Category;
use Drupal\app_contract\Contract\SiteMap\Section;
use Drupal\app_contract\Contract\SiteMap\SiteMap;
use Drupal\app_contract\Contract\SiteMap\SiteMapBuilder;

final readonly class BlogSiteMap implements SiteMapBuilder {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function build(): SiteMap {
    $sitemap = new SiteMap();
    $category = new Category(new TranslatableMarkup('Blog'));
    $section = new Section(new TranslatableMarkup('Articles'));

    foreach ($this->articles() as $article) {
      \assert($article instanceof Article);
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
